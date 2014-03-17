<?php

class User extends CActiveRecord
{
	const STATUS_NOACTIVE=0;
	const STATUS_ACTIVE=1;
	const STATUS_BANNED=-1;
	
	//TODO: Delete for next version (backward compatibility)
	const STATUS_BANED=-1;
/**	
	The followings are the available columns in table 'user':
 * @property integer $idUser
 * @property string $username
 * @property string $usertype
 * @property string $password
 * @property string $email
 * @property string $activationKey
 * @property string $userSince
 * @property string $lastActive
 * @property integer $status
 * @property integer $dataprivacyStatementAccepted
 * @property string $language
 *
 * The followings are the available model relations:
 * @property Tan[] $tans
 * @property Tan[] $tans1
 * @property Testrun[] $testruns
 * @property Token[] $tokens
 * @property UserGroupRel[] $userGroupRels
 * @property UserMeta[] $userMetas
 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return Yii::app()->getModule('user')->tableUsers;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.CConsoleApplication
		return ((get_class(Yii::app())=='CConsoleApplication' || (get_class(Yii::app())!='CConsoleApplication' && Yii::app()->getModule('user')->isAdmin()))?array(
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
			array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('status', 'in', 'range'=>array(self::STATUS_NOACTIVE,self::STATUS_ACTIVE,self::STATUS_BANNED)),
			array('superuser', 'in', 'range'=>array(0,1)),
            array('userSince', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
            array('lastActive', 'default', 'value' => '0000-00-00 00:00:00', 'setOnEmpty' => true, 'on' => 'insert'),
			array('username, email, superuser, status', 'required'),
			array('superuser, status', 'numerical', 'integerOnly'=>true),
			array('idUser, username, password, email, activationKey, userSince, lastActive, superuser, status', 'safe', 'on'=>'search'),
		):((Yii::app()->user->id==$this->idUser)?array(
			array('username, email', 'required'),
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
		):array()));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		
		$relations = Yii::app()->getModule('user')->relations;
		
		$relations = array_merge($relations, array(
		'tans' => array(self::HAS_MANY, 'Tan', 'createdBy'),
		'tans1' => array(self::HAS_MANY, 'Tan', 'userId'),
		'testruns' => array(self::HAS_MANY, 'Testrun', 'userId'),
		'tokens' => array(self::HAS_MANY, 'Token', 'createdBy'),
		'userGroupRels' => array(self::HAS_MANY, 'UserGroupRel', 'userId'),
		'userMetas' => array(self::HAS_MANY, 'UserMeta', 'userId'),
		));
		

		if (!isset($relations['profile']))
			$relations['profile'] = array(self::HAS_ONE, 'Profile', 'user_id');
		return $relations;		
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>UserModule::t("username"),
			'password'=>UserModule::t("password"),
			'verifyPassword'=>UserModule::t("Retype Password"),
			'email'=>UserModule::t("E-mail"),
			'verifyCode'=>UserModule::t("Verification Code"),
			'idUser' => UserModule::t("Id"),
			'activationKey' => UserModule::t("activation key"),
			'userSince' => UserModule::t("Registration date"),
			'lastActive' => UserModule::t("Last visit"),
			'status' => UserModule::t("Status"),
			'activationKey' => 'Activation Key',
			'dataprivacyStatementAccepted' => 'Dataprivacy Statement Accepted',
			'language' => 'Language',
		);
	}
	
	public function scopes()
    {
        return array(
            'active'=>array(
                'condition'=>'status='.self::STATUS_ACTIVE,
            ),
            'notactive'=>array(
                'condition'=>'status='.self::STATUS_NOACTIVE,
            ),
            'banned'=>array(
                'condition'=>'status='.self::STATUS_BANNED,
            ),
            'superuser'=>array(
                'condition'=>'superuser=1',
            ),
            'notsafe'=>array(
            	'select' => 'idUser, username, password, email, activationKey, userSince, lastActive, superuser, status',
            ),
        );
    }
	
	public function defaultScope()
    {
          return CMap::mergeArray(Yii::app()->getModule('user')->defaultScope,array(
            'alias'=>'user',
            'select' => 'user.idUser, user.username, user.email, user.userSince, user.lastActive, user.superuser, user.status',
        ));
    }
	
	public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'UserStatus' => array(
				self::STATUS_NOACTIVE => UserModule::t('Not active'),
				self::STATUS_ACTIVE => UserModule::t('Active'),
				self::STATUS_BANNED => UserModule::t('Banned'),
			),
			'AdminStatus' => array(
				'0' => UserModule::t('No'),
				'1' => UserModule::t('Yes'),
			),
		);
		if (isset($code))
			return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
		else
			return isset($_items[$type]) ? $_items[$type] : false;
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
	
		$criteria=new CDbCriteria;
	
		$criteria->compare('idUser',$this->idUser);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('activationKey',$this->activationKey);
		$criteria->compare('userSince',$this->userSince);
		$criteria->compare('lastActive',$this->lastActive);
		$criteria->compare('superuser',$this->superuser);
		$criteria->compare('status',$this->status);
	
		return new CActiveDataProvider(get_class($this), array(
				'criteria'=>$criteria,
				'pagination'=>array(
						'pageSize'=>Yii::app()->getModule('user')->user_page_size,
				),
		));
	}
}