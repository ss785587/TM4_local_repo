<?php

class InitializationController extends Controller
{
		
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('index','initUserChoise'),
						'users'=>array('@'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	
	/**
	 * TEST-ENGINE INITIALIZATION
	 */
	public function actionIndex()
	{	
		$session=new CHttpSession;
		$session->open();
		$userId =  $session['userId'];
		$uberTestId = $session['uberTestId'];
		
		//if session is not set, redirection to frontend
		if(!isset($userId) || !isset($uberTestId)){
			header('Location:'.REL_PATH_FRONTEND);
			die();
		}
		
		//check, if the user have already started the given uberTest
		$testRuns = $this->getAvailableTestRuns($uberTestId, $userId);
		if(isset($testRuns) && count($testRuns)!=0){
			//list testruns to user
			$this->render('index', array('dataProvider'=>$testRuns));
		}else{
			//start new test run
			$testRun = $this->initNewTestRun($uberTestId, $userId);
			//start TEST-ENGINE
			$this->startTestEngineLoop($testRun);
		}
	}
	
	/**
	 * Creates a new TestRun based on the related testdefinion to the given uberTest.
	 * @param integer $uberTestId uberTest Id
	 * @param integer $userId user Id
	 * @return TestRun new created TestRun
	 */
	public function initNewTestRun($uberTestId, $userId){
		$testDefContr = new TestDefinitionController('TestDefinitionController');
		$testDef = $testDefContr->getRunnableTestDefinition($uberTestId);
		
		$testRun = new TestRun();
		$testRun->jsonData = $testDef->testBlueprint;
		$testRun->userId = $userId;
		$testRun->testDefinitionId = $testDef->idTestDefinition;
		//TODO: update userId and co to JSON file
	 	$testRun->save();
	 	
	 	return $testRun;
	}
	
	/**
	 * Method gets user choise of TestRuns via HTTP-GET request. If no testRun Id is transmitted,
	 * the user has chosen to start a new test. Based on the user choise, the test engine will be 
	 * started with the given TestRun.
	 *  
	 * @param string $testRunId Id of chosen TestRun
	 * @param string $newTest true, if the user want to start a new test
	 * @throws CHttpException thrown, if userId in the session in different ot the userId saved in the TestRun
	 */
	public function actionInitUserChoise($testRunId=null, $newTest=false){
		if(!isset($testRunId) && $newTest==false){
			throw new CHttpException(404,'The requested page does not exist.');
		}
		//load session
		$session=new CHttpSession;
		$session->open();
		$userId =  $session['userId'];
		$uberTestId = $session['uberTestId'];
		//check user choise
		if($newTest){
			$testRun = $this->initNewTestRun($uberTestId,$userId);
		}else{
			$testRun = TestRunController::loadModel($testRunId);
			if($testRun->userId != $userId){
				throw new CHttpException(404,'The requested page does not exist.');
			}
			//TODO: change JSON-META data
		}
		
		//start TEST-ENGINE
		$this->startTestEngineLoop($testRun);
		
	}
	
	public function startTestEngineLoop($testRun){
		echo "TESTENGINE STARTED";
	}
	
	/**
	 * Checks, if the the given user has already started the given uberTest.
	 * If the testrun has one of the following status, the test run will be returned.
	 * Allowed status: TestRun::STATUS_NEW, TestRun::STATUS_STARTED, TestRun::STATUS_PAUSED or TestRun::STATUS_CONTINUED
	 * 
	 * @param integer $uberTestId UberTest Id
	 * @param integer $userId User Id
	 * @return testruns 
	 */
	public function getAvailableTestRuns($uberTestId, $userId){
		$testRuns = TestRunController::getTestRunsToUberTest($uberTestId, $userId);
		if(isset($testRuns)){
			//check status of testruns
			$count = count($testRuns);
			for($i=0; $i<$count; $i++){
				$run = $testRuns[$i];
				if(TestRun::STATUS_NEW==$run->status || TestRun::STATUS_STARTED==$run->status ||
				TestRun::STATUS_PAUSED==$run->status || TestRun::STATUS_CONTINUED==$run->status){
					continue;
				}else{
					//remove from array
					unset($testRuns[$i]);
				}
			}
		}
		return $testRuns;
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