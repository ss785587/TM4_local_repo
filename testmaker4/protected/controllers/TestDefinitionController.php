<?php

class TestDefinitionController extends Controller
{
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return TestDefinition the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=TestDefinition::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * Returns one or more models based on the criteria given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param  CDbCriteria criteria of the models to be loaded
	 * @return TestDefinition the loaded models
	 * @throws CHttpException
	 */
	public function loadModelsWithCriteria($criteria){
		$model=TestDefinition::model()->findAll($criteria);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 *Returns all UberTest Ids, which have at least one valid TestDefintion.
	 */
	public function getActiveUberTestIds(){
		$validUberTestIds = array();
		//iterate over all TestDefinitions
		$dataProvider=new CActiveDataProvider('TestDefinition');
		foreach($dataProvider->getData() as $testDef){
			//check if related UberTest from TestDefinition already exists in array
			if(in_array($testDef->uberTestId, $validUberTestIds)){
				continue;
			}else{
				if($this->checkAvailability($testDef)){
					array_push($validUberTestIds,$testDef->uberTestId);
				}	
			}
		}
		return $validUberTestIds;
	}
	
	/**
	 * Returns all TestDefinitions, which belongs to the given UberTest
	 * @param integer $uberTestId UberTest Id
	 * @return array of TestDefinitions
	 */
	public function getAllTestDefinition($uberTestId){
		//Returns all availiable Testdefinition the given UberTest Id
		$criteria=new CDbCriteria;
		$criteria->condition='uberTestId = :uberTestId';
		$criteria->params=array(':uberTestId'=>$uberTestId);
		$models = $this->loadModelsWithCriteria($criteria);
		return $models;
	}
	
	/**
	 * Returns one valid testdefintion to the given uberTest
	 * @param unknown $uberTestId - UberTest Id
	 * @return TestDefinition
	 */
	public function getRunnableTestDefinition($uberTestId){
		//get all testdefinition to given ubertest + call checkAvailability($testDefId)
		$models = $this->getAllTestDefinition($uberTestId);
		
		//check availability of TestDefinitions
		for($i=0; $i<count($models); $i++){
			if(!$this->checkAvailability($models[$i])){
				//delete from arry
				unset($models[$i]);
			}
		}
		
		//calculate probability
		$testDef = $this->calculateProbability($models);
		return $testDef;
	}
	
	/**
	 * Checks, if a TestDefinition is availabile with the following attributes:
	 * start-/endtime, active, maxNum, probalitiy, allowServalTimes
	 * 
	 * @param integer $testDefId -  id of TestDefinition 
	 * @throws CDbException - thrown if given TestDefinitionId is invalid
	 * @return boolean|string - return true, if TestDefinition is ready to use
	 */
	public function checkAvailability($model){
		
		if(!isset($model)){
			throw new CDbException('Testdefinition does not exist.');
		}
		
		//has to check following attributes: start-/endtime, active, maxNum, probalitiy, allowServalTimes
		if(!$model->active){
			return false;
		}
		if(!(0 == $model->maxNum) && ($model->currentNum >= $model->maxNum)){
			return false;
		}
		//check, if starttime already passed
		if(null === $model->startTime || strtotime($model->startTime) > time()){
			return false;
		}
		//check, if endtime already passed (null = never ends)
		if(!(null === $model->endTime) && (strtotime($model->endTime) < time())){
			return false;
		}
		//check if probalitiy = 0
		if(0 === $model->probability){
			return false;
		} 
		//TODO: allowServalTimes
		return true;
	}
	
	/**
	 * Calculates probability and select one TestDefinition from the given one
	 * @param  $models - array of TestDefinions
	 * @return TestDefinition
	 */
	public function calculateProbability($models){
		//calculate total sum and build up probability map
		$sum = 0;
		$probabilityMap = array();
		foreach($models as $testDef){
			$sum += $testDef->probability;
			$probabilityMap[$sum] = $testDef;
		}		
		//generate random number between 1 and total sum
		$rand = rand(1,$sum);
		
		//get TestDefinition with probability map and random number
		foreach($probabilityMap as $key => $value){
			if($rand <= $key){
				return $value;
			}
		}
		
	}

}