<?php

class UberTestController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
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
				'actions'=>array('index','view','startTest'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','view','startTest', 'continue','showCertificate','initUserChoise'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	
	/**
	 * Lists all available ubertests to the user.
	 */
	public function actionIndex()
	{
		//get all valid UberTest from TestDefinitionController
		$testDefContr = new TestDefinitionController('TestDefinitionController');
		//returns array of UberTestIds
		
		$uberTestIds = $testDefContr->getActiveUberTestIds();
		$dataProvider = array();
		foreach($uberTestIds as $id){
			if(Yii::app()->user->isGuest){
				$data = $this->generateSubTestViewData($id);
			}else{
				//logged in user
				//get user TestRuns
			$testRuns = TestRunController::getTestRunsToUberTest($id, Yii::app()->user->id);
			$data = $this->generateSubTestViewData($id, $testRuns);
			}
			array_push($dataProvider, $data);
		}

		//render page
		$this->render('//site/index',array(
			'dataProvider'=>$dataProvider,
		));
	}
	
	/**
	 * Generates a datastructure object of a subtest, which has all necessary information for rendering.
	 * @param integer $uberTestId
	 * @param string $testRuns
	 * @return stdClass object for rendering a subtest
	 */
	private function generateSubTestViewData($uberTestId, $testRuns=null){
		//create object with values and meta-data
		$data = new stdClass();
		$data->values = $this->loadModel($uberTestId);
		$data->meta = array("showCertBt"=>false, "continueBt"=>false, "startBt"=>false);
		//start Test-Button
		if(!isset($testRuns) || count($testRuns)==0){
			$data->meta["startBt"] = true;
		}
		else{
			//check for testruns that are already finished
			foreach($testRuns as $testRun){
				$testDef = TestDefinitionController::loadModel($testRun->testDefinitionId);
				$allowServeralTimes = $testDef->allowServeralTimes;
				if(TestRun::model()->isFinished($testRun->status)){
					$data->meta["showCertBt"] = true;
				}else{
					$data->meta["continueBt"] = true;
				}
				if($allowServeralTimes){
					$data->meta["startBt"] = true;
				}
			}
		}
		return $data;
	}
	
	/**
	 * Redirection to overview of certificates in user profil.
	 * @param integer $id id of uberTest
	 */
	public function actionShowCertificate($id){
		//TODO: show certificate or redirect to certificate overview	
		$this->redirect(array("/user/profile"));
	}
	
	/**
	 * Starts TestEngine and continues with given uberTest.
	 * @param integer $id id of uberTest
	 */
	public function actionContinue($id){
		$id = htmlspecialchars($id);
		//check, if user have already started the given uberTest
		$testRuns = TestRunController::getAvailableTestRuns($id, Yii::app()->user->idUser);
		if(isset($testRuns)){
			if(count($testRuns)==1){
				//save testrun in session and start TestEngine
				$this->startTestEngine(null, $testRuns[0]);
			}else{
				//list testruns to user
				$this->render('//testrunInit/index', array('dataProvider'=>$testRuns));
			}
		}else{
			throw new CHttpException(404);
		}
	}
	
	/**
	 * Select test from testDefinition and starts the TestEngine.
	 * @param integer $id id of uberTest
	 */
	public function actionStartTest($id){
		if(Yii::app()->user->isGuest){
			Yii::app()->user->returnUrl = REL_PATH_FRONTEND;
			$this->redirect(array('user/login'));
		}else{
			$this->startTestEngine($id);
		}
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
	public function actionInitUserChoise($testRunId=null){
		if(!isset($testRunId)){
			throw new CHttpException(404,'The requested page does not exist.');
		}
				
		//check user choise
		$testRun = TestRunController::loadModel($testRunId);
		if($testRun->userId != Yii::app()->user->id){
			throw new CHttpException(404,'The requested page does not exist.');
		}
		//TODO: change JSON-META data

		//save testrun in session and start TestEngine
		$this->startTestEngine(null,$testRun);
	
	}
	
	/**
	 * Prepares session and starts TestEngine.
	 */
	private function startTestEngine($uberTestId,$testRun=null){
		//delete previous session, if still exists
		Utils::destroyTESession();
		//save session to start testengine
		Yii::app()->session['uberTestId']=$uberTestId;
		Yii::app()->session['userId']=Yii::app()->user->idUser;
		
		if(isset($testRun)){
			Yii::app()->session['TE_testrunDbObj'] = $testRun;
		}	
		//redirect to testengine
		//start new test run
		header('Location: '. REL_PATH_TESTENGINE);
		die();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return UberTest the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=UberTest::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param UberTest $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='uber-test-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
