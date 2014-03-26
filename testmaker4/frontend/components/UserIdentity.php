<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {
	
	private $_id = 1;
		
	/**
	 * @see CUserIdentity::authenticate()
	 */
	public function authenticate() {
		//username can be email or just the username
		$validator = new CEmailValidator;
		if($validator->validateValue($this->username)){
			$user=User::model()->find('LOWER(email)=?',array(strtolower($this->username)));
		}else{
			$user=User::model()->find('LOWER(username)=?',array(strtolower($this->username)));
		}	
   
		if ($user === null)
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		else if (! $user->validatePassword ( $this->password ))
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		else {
			$this->_id = $user->idUser;
			$this->username = $user->username;
			//setState are saved in Cookie
			$this->setState('lastActive', date("m/d/y g:i A", strtotime($user->lastActive)));
			$user->saveAttributes (array('lastActive' => date ("Y-m-d H:i:s", time ())) );
			$this->errorCode = self::ERROR_NONE;
		}
		return $this->errorCode == self::ERROR_NONE;
	}
	
	/**
	 * @see CUserIdentity::getId()
	 */
	public function getId() {
		return $this->_id;
	}
}