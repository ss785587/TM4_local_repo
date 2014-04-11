<?php

class InputController extends Controller
{
	/** properties */
	private $testRunDbObj;
	private $testRunObj;
	
	public function actionIndex(){
		//get session values
		$this->testRunDbObj = Yii::app()->session['TE_testrunDbObj'];
		$this->testRunObj = Yii::app()->session['TE_jsonObj'];
		if(!isset($this->testRunDbObj) || !isset($this->testRunObj)){
			throw new CException('No TestRun in update loop');
		}
		
		$data = Yii::app()->session['TE_results'];
		// validate $data	
		$this->prepareData($data);
		// (1)save user input to db - table: testRunEval
		$this->saveToDBTable($data);		
		//(2)save to testRunObj + testRunObj to db (JSON-file update)
		$this->saveToTestRun($data);
		Yii::app()->session['TE_step']=TestEngineController::TE_STEP_UPDATE;
		$this->redirect($this->createUrl("testEngine/index"));
	}
	
	
	/**
	 * Updates the data of the TestRun and saves the TestRunObject to database.
	 * Expect that the valditaion function was called before.
	 * @param object $data data to update testrun
	 */
	private function saveToTestRun($data){
		if($data!=null){
			if(is_array($data)){
				foreach ($data as $element){
					//by id could be item scale or element
					$testRunElement = $this->testRunObj->getValueById($element->element_id);
					if(isset($testRunElement)){
						//update element
						$testRunElement->value = $element->value;
					}else{
						//element with id not found
						//TODO: write to log
					}					
				}
				//save changes to db
				$this->testRunDbObj->jsonData = TestRunParser::encodeToJson($this->testRunObj);
				$this->testRunDbObj->save();
			}
		}
	}
	
	/**
	 * Saves given user test input to TestRunEval database table.
	 * @param array $data
	 */
	private function saveToDBTable($data){
		if(isset($data)){
			foreach ($data as $element){
				//check if already object exists
				$obj = TestRunEval::model()->find("elementId=:element_id AND testRundId=:testRunId",
						array(":element_id"=>(int)$element->element_id, ":testRunId"=>(int)$this->testRunDbObj->idTestRun));
				if(!isset($obj)){
					$obj = new TestRunEval();
				}
				$obj->elementId = (int)$element->element_id;
				$obj->value =$element->value;
				//TODO: differentiate between scales and items
				$obj->elementType = 1;//$element->type;
				$obj->testRundId = (int)$this->testRunDbObj->idTestRun;
				$err = $obj->save();
				if(!$err){
					//TODO: save to log
				}
			}
			 
		}
	}
	
	/**
	 * Validates user data and sets missing values to null.
	 * If on given element has no id, it will be removed.
	 * @param array $data
	 */
	private function prepareData($data){
		if($data!=null){
			if(is_array($data)){
				for($i=0; $i<count($data); $i++){
					$element = $data[$i];
					//if element id not exists, log to timelog and delete from array
					if(property_exists($element,"element_id")){
						$id = $element->element_id;
					}else{
						//TODO: write to timelog
						unset($data[$i]);
					}
					//set value
					$element->value = (property_exists($element,"value"))? $element->value : null;
					//set type
					$element->type = (property_exists($element,"type"))? $element->type : null;
					//set display
					$element->display = (property_exists($element,"display"))? $element->display : null;
					//by id could be item scale or element
				}
			}
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