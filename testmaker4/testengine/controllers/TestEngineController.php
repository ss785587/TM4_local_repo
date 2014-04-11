<?php

class TestEngineController extends Controller
{
	/** STEP Constants*/
	const TE_STEP_INIT = 0;
	const TE_STEP_INPUT = 1;
	const TE_STEP_UPDATE = 2;
	const TE_STEP_RENDER = 3;
	const TE_STEP_FINISH = 4;
	
	/** PAGE DIRECTIONS Constants */
	const TE_PAGE_FORWARD = 1;
	const TE_PAGE_BACKWARD = 0;
	const TE_PAGE_REFRESH = 2;
	const TE_PAGE_ABORT = 3;
	
	/** HTTP AJAX STATUS Constants*/
	const NO_CLIENT_STATE = -100;
	const HTTP_STATUS_OK = 200;
	const HTTP_STATUS_RENDERING_OK = 201;
	const HTTP_STATUS_REDIRECT = 308;
		
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('index'),
						'users'=>array('@'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	
	/**
	 * ENTRY POINT OF TEST-ENGINE
	 */
	public function actionIndex(){
		$step = Yii::app()->session['TE_step'];
		if(!isset($step)){
			$step = 0;
		}
		if(Yii::app()->request){
			$data = $this->handleRequest();
			Yii::app()->session['TE_results'] = $data;
		}
		
		switch ($step){
		
			case self::TE_STEP_INIT:
				$this->forward('initialization/index');
				break;
			case self::TE_STEP_INPUT:
				$this->forward('input/index');
				break;
			case self::TE_STEP_UPDATE:
				$this->forward('update/index');
				break;
			case self::TE_STEP_RENDER:
				$this->forward('render/index');
				break;
			case self::TE_STEP_FINISH:
				//delete session
				$this->destroyTESession();
				//abort AJAX call
				if(Yii::app()->request->isAjaxRequest){
					//redirect to frontend
					self::responeAjaxRequest('http://'.$_SERVER['HTTP_HOST'].REL_PATH_FRONTEND,self::HTTP_STATUS_REDIRECT);
				}else{
					header('Location: '. $_SERVER['HTTP_HOST'].REL_PATH_FRONTEND);
				}
				die();
			default:
				throw new CHttpException(404,'The requested page does not exist.');
		}
	}
	
	/**
	 * Send data to ajax request.
	 * @param json string $data
	 */
	public static function responeAjaxRequest($data,$status=self::HTTP_STATUS_OK){
		header('Content-Type: application/json; charset="UTF-8"');
		echo CJSON::encode(array('status'=>$status,'data'=>$data));
	}
	
	public function handleRequest(){
		//check client status
		$client_status = self::NO_CLIENT_STATE;
		if(isset($_POST['status'])){
			$client_status = htmlspecialchars($_POST['status']);
		}
		
		switch ($client_status) {
			case self::HTTP_STATUS_OK:
				//set direction
				if(isset($_POST['direction'])){
					Yii::app()->session['TE_PageDirection'] = htmlspecialchars($_POST['direction']);
				}
				//return user data
				if(isset($_POST['ajaxData'])){
					$data = TestRunParser::json_decode($_POST['ajaxData']);
					//validate client data
					$data = TestRunParser::validateClientData($data);
					return $data;
				}
			break;
			
			case self::HTTP_STATUS_RENDERING_OK:
				//wirte to timelog
				//return HTTP OK flag
				self::responeAjaxRequest("",self::HTTP_STATUS_OK);
				die();
			break;
			
			case self::NO_CLIENT_STATE:
				//fall through
			default:
				//TODO: save to timelog;
				
			break;
		}
		
	}
	
	/**
	 * Destroys all session values of TestEngine 
	 */
	public function destroyTESession(){
		unset(Yii::app()->session['TE_pageDirection']);
		unset(Yii::app()->session['TE_testrunDbObj']);
		unset(Yii::app()->session['TE_jsonObj']);
		unset(Yii::app()->session['TE_curSubtestPointer']);
		unset(Yii::app()->session['TE_curPageArrPointer']);
		unset(Yii::app()->session['TE_step']);
		unset(Yii::app()->session['uberTestId']);
	}
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('//site/error', $error);
		}
	}

}