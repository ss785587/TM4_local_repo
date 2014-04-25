<?php
class Utils{

	/**
	 * Destroys all session values of TestEngine
	 */
	public static function destroyTESession(){
		unset(Yii::app()->session['TE_pageDirection']);
		unset(Yii::app()->session['TE_testrunDbObj']);
		unset(Yii::app()->session['TE_jsonObj']);
		unset(Yii::app()->session['TE_step']);
		unset(Yii::app()->session['uberTestId']);
	}
}