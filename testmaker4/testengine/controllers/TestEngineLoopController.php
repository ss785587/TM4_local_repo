<?php

class TestEngineLoopController extends Controller
{
		
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
	 * TEST-ENGINE LOOPController.
	 * A TestEngine loop has three stages.
	 * (1) Input
	 * (2) Update
	 * (3) Render
	 * The loop will go through these three stages until the test is aborted or finished. 
	 */
	public function startLoop()
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