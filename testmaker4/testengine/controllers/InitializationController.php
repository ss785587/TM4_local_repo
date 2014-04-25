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
		$userId =  Yii::app()->session['userId'];
		$uberTestId = Yii::app()->session['uberTestId'];
		$testRun = Yii::app()->session['TE_testrunDbObj'];
		
		//if session is not set, redirection to frontend
		if(!(isset($userId) && isset($uberTestId)) && !(isset($userId) && isset($testRun))){
			//redirect to frontend
			//$this->redirect(REL_PATH_FRONTEND);
			$this->render('//site/error', array('code'=>'404', 'message'=>'Invalid user session. Redirection to frontpage...'));
			die();
		}

		if(!isset($testRun)){
			//start new test
			$testRun = $this->initNewTestRun($uberTestId, $userId);	
		}
		//start TEST-ENGINE
		$this->startTestEngineLoop($testRun);
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
	 * Start TestEngine loop and parse JSON-TestRun.
	 * @param unknown $testRun
	 */
	public function startTestEngineLoop($testRunDbObj){
		//parse JSON TestRun
		$testRunObj = TestRunParser::decodeToTestRunObj($testRunDbObj->jsonData);
		//set session
		Yii::app()->session['TE_testrunDbObj'] = $testRunDbObj;
		Yii::app()->session['TE_jsonObj'] = $testRunObj;
		//skip INPUT step and begin with UPDATE
		Yii::import('application.controllers.TestEngineController');
		Yii::app()->session['TE_step'] = TestEngineController::TE_STEP_UPDATE;
		$this->includeJsFiles();
		//Render TestEngine Template
		$this->render('//TE_index', array('dataProvider'=>null));
		//$this->redirect($this->createUrl("testEngine/index"));
	}
	
	/**
	 * Includes all JavaScript files.
	 */
	private function includeJsFiles(){
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/testengine.js');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/TE_update.js');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/TE_render.js');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/calculation.js');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/TestRunDelegator.js');
		Yii::app()->clientScript->registerCoreScript('jquery');
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