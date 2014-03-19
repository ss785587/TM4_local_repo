<?php

class LoginController extends Controller
{
	public $defaultAction = 'login';

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (Yii::app()->user->isGuest) {
			$model=new UserLogin;
			// collect user input data
			if(isset($_POST['UserLogin']))
			{
				$model->attributes=$_POST['UserLogin'];
				// validate user input and redirect to previous page if valid
				if($model->validate()) {
					$this->lastViset();	
					$hasCurrentVer = $this->checkDataPrivacyStatement();
					
					if($hasCurrentVer){
						//redirect
						if(Yii::app()->user->returnUrl=='/index.php')
							$this->redirect(Yii::app()->controller->module->returnUrl);
						else
							$this->redirect(Yii::app()->user->returnUrl);
					}else{
						//show dataprivacy
						$dpComp = new DataPrivacy();
						$language = System::getValue(System::FRONTEND_LANGUAGE_ACTIVE);
						$privacyStatement = $dpComp->getLastestDataPrivacyDescription($language);
						$this->render('//dataPrivacyDefinition/index',array('statementDescription'=>$privacyStatement));
					}
				}else{
				// display the login form
					$this->render('/user/login',array('model'=>$model));
				}
			}else{
				// display the login form
					$this->render('/user/login',array('model'=>$model));
				}
		} else
			$this->redirect(Yii::app()->controller->module->returnUrl);
	}
	
	public function actionDataprivacyAccepted(){
		$currentPrivacyVersion = System::getValue(System::DATAPRIVACY_CURRENT_VERSION);
		$tmpUser = User::model()->notsafe()->findByPk(Yii::app()->user->id);
		$tmpUser->dataprivacyStatementAccepted = $currentPrivacyVersion;
		$tmpUser->save();
		
		$this->redirect(Yii::app()->user->returnUrl);
	}
	
	public function actionDataprivacyDecline(){
		Yii::app()->user->logout();	
		$this->redirect(Yii::app()->user->returnUrl);
	}
	
	public function actionDeleteAccount(){
		$tmpUser = User::model()->notsafe()->findByPk(Yii::app()->user->id);
		$tmpUser->status = User::STATUS_DELETE;
		$tmpUser->save();
		
		Yii::app()->user->logout();	
		$this->redirect(Yii::app()->user->returnUrl);
	}
	
	private function lastViset() {
		$lastVisit = User::model()->notsafe()->findByPk(Yii::app()->user->id);
		$lastVisit->setLastActive(time());
		$lastVisit->save();
	}
	
	/**
	 *	Checks the accepted dataprivacy-statment with the current system version. 
	 *	@return false, both versions dont match
	 *			true, both versions match
	 */
	private function checkDataPrivacyStatement(){
		$tmpUser = User::model()->notsafe()->findByPk(Yii::app()->user->id);
		$userVersion = $tmpUser->dataprivacyStatementAccepted;
		
		$currentPrivacyVersion = System::getValue(System::DATAPRIVACY_CURRENT_VERSION);
		
		if($userVersion === $currentPrivacyVersion){
		 	return true;
		}else{
			return false;
		}
	}

}