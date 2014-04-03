<?php

class UpdateController extends Controller
{
		/**
		 * ENTRY POINT UPDATE
		 */
	public function actionIndex(){
		$testRunDbObj = Yii::app()->session['TE_testrunDbObj'];
		if(!isset($testRunDbObj)){
			throw new CException('No TestRun in update loop');
		}
		//save Object to JSON-String
		$testRunObj = TestRunParser::parseJson($testRunDbObj->jsonData);
		//recalculate and update variables
		$testRunObj->updateVariables();
		
		//save changes to db
		$testRunDbObj->jsonData = TestRunParser::encodeToJson($testRunObj);
 		$testRunDbObj->save();
 		
 		//redirect to next loop step
 		Yii::app()->session['TE_jsonObj'] = $testRunObj;
  		Yii::app()->session['TE_step'] = TestEngineController::TE_STEP_RENDER;
 		$this->redirect($this->createUrl("testEngine/index"));
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

}