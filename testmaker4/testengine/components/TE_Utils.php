<?php
class TE_Utils{

	/**
	 * saves testRunDb Object to database. 
	 * If no parameter is given, try to get objects form session.
	 */
	public static function saveTestRunToDb($testRunDbObj=null, $testRunObj=null){
		if(!isset($testRunDbObj)){
			$testRunDbObj =  Yii::app()->session['TE_testrunDbObj'];
		}
		if(!isset($testRunObj)){
			$testRunObj =  Yii::app()->session['TE_jsonObj'];
		}
		if(isset($testRunObj) && isset($testRunDbObj)){
			$testRunDbObj->jsonData = TestRunParser::encodeToJson($testRunObj);
			$testRunDbObj->save();
		}
	}
}