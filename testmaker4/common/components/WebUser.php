<?php

class WebUser extends CWebUser
{

    public function getRole()
    {
        return $this->getState('__role');
    }
    
    public function getId()
    {
        return $this->getState('__id') ? $this->getState('__id') : 0;
    }

//    protected function beforeLogin($id, $states, $fromCookie)
//    {
//        parent::beforeLogin($id, $states, $fromCookie);
//
//        $model = new UserLoginStats();
//        $model->attributes = array(
//            'idUser' => $id,
//            'ip' => ip2long(Yii::app()->request->getUserHostAddress())
//        );
//        $model->save();
//
//        return true;
//    }

    protected function afterLogin($fromCookie)
	{
        parent::afterLogin($fromCookie);
        $this->updateSession();
	}

    public function updateSession() {
        $user = Yii::app()->getModule('user')->user($this->id);
        $userAttributes = array(
        										'idUser'=>$user->idUser,
                                                'email'=>$user->email,
                                                'username'=>$user->username,
                                                'userSince'=>$user->userSince,
                                                'lastActive'=>$user->lastActive,
                                           );
        foreach ($userAttributes as $attrName=>$attrValue) {
            $this->setState($attrName,$attrValue);
        }
    }

    public function model($id=0) {
        return Yii::app()->getModule('user')->user($id);
    }

    public function user($id=0) {
        return $this->model($id);
    }

    public function getUserByName($username) {
        return Yii::app()->getModule('user')->getUserByName($username);
    }

    public function getAdmins() {
        return Yii::app()->getModule('user')->getAdmins();
    }

    public function isAdmin() {
        return Yii::app()->getModule('user')->isAdmin();
    }

}