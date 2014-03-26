<?php

class TestRunController extends Controller
{
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return TestDefinition the loaded model
	 * @throws CHttpException
	 */
	public static function loadModel($id)
	{
		$model=TestRun::model()->findByPk($id);
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
	public static function loadModelsWithCriteria($criteria){
		$model=TestRun::model()->findAll($criteria);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * Returns one or more models based on the Sql query given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param  string SQL query
	 * @param  string parameters
	 * @return TestDefinition the loaded models
	 * @throws CHttpException
	 */
	public static function loadModelsWithSql($sql, $parameter){
		$model = TestRun::model()->findAllBySql($sql,$parameter);	
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * Returns all TestRuns, which belongs to the given uberTest Id.
	 * If there is a user specified, self method only returns the TestRuns to self User.
	 * @param integer $uberTestId
	 * @param string $userId
	 * @return array of models
	 */
	public static function getTestRunsToUberTest($uberTestId, $userId=null){
		if(isset($userId)){
			//with userId
			$sqlQuery = 'SELECT * FROM testrun, testdefinition WHERE testrun.userId = :userId AND testrun.testDefinitionId = testdefinition.idTestDefinition AND testdefinition.uberTestId = :uberTestId';
			$para = array(':uberTestId'=>$uberTestId, ':userId'=>$userId);
			$models = self::loadModelsWithSql($sqlQuery,$para);
			return $models;
		}else{
			//without userId
			$sqlQuery = 'SELECT * FROM testrun, testdefinition WHERE testrun.testDefinitionId = testdefinition.idTestDefinition AND testdefinition.uberTestId = :uberTestId';
			$para = array(':uberTestId'=>$uberTestId);
			$models = self::loadModelsWithSql($sqlQuery,$para);		
			return $models;
		}
	}
	

}