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
				//delete session and redirect to frontend
				$this->destroyTESession();
				header('Location: '. REL_PATH_FRONTEND);
				die();
			default:
				throw new CHttpException(404,'The requested page does not exist.');
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
	 * TEST-ENGINE LOOPController.
	 * A TestEngine loop has three stages.
	 * (1) Input
	 * (2) Update
	 * (3) Render
	 * The loop will go through these three stages until the test is aborted or finished. 
	 */
	public function actionStartLoop()
	{	
		
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