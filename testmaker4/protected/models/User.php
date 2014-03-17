<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
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
class User extends CActiveRecord
{
	public $password_repeat;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, username', 'unique'),
			array('email', 'email'),
			array('password', 'compare', 'compareAttribute'=>'password_repeat'),
			array('password_repeat', 'safe'),
			array('email, username, password, password_repeat', 'required'),
			array('username, password, email', 'length', 'max'=>255),
			array('language', 'length', 'max'=>2),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idUser, username, usertype, email, activationKey, userSince, lastActive, status, dataprivacyStatementAccepted, language', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'tans' => array(self::HAS_MANY, 'Tan', 'createdBy'),
			'tans1' => array(self::HAS_MANY, 'Tan', 'userId'),
			'testruns' => array(self::HAS_MANY, 'Testrun', 'userId'),
			'tokens' => array(self::HAS_MANY, 'Token', 'createdBy'),
			'userGroupRels' => array(self::HAS_MANY, 'UserGroupRel', 'userId'),
			'userMetas' => array(self::HAS_MANY, 'UserMeta', 'userId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idUser' => 'Id User',
			'username' => 'Username',
			'usertype' => 'Usertype',
			'password' => 'Password',
			'email' => 'Email',
			'activationKey' => 'Activation Key',
			'userSince' => 'User Since',
			'lastActive' => 'Last Active',
			'status' => 'User Status',
			'dataprivacyStatementAccepted' => 'Dataprivacy Statement Accepted',
			'language' => 'Language',
		);
	}

	/**
	 * @return array with behaviors
	 */
	public function behaviors()
	{
		return array(
				'CTimestampBehavior' => array(
						'class' => 'zii.behaviors.CTimestampBehavior',
						'createAttribute' => 'userSince',
						'updateAttribute' => 'lastActive',
						'setUpdateOnCreate' => true,
				),
		);
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('idUser',$this->idUser);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('usertype',$this->usertype,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('activationKey',$this->activationKey,true);
		$criteria->compare('userSince',$this->userSince,true);
		$criteria->compare('lastActive',$this->lastActive,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('dataprivacyStatementAccepted',$this->dataprivacyStatementAccepted);
		$criteria->compare('language',$this->language,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * apply a hash on the password before we store it in the database
	 */
	protected function afterValidate()
	{
		parent::afterValidate();
		if(!$this->hasErrors())
			$this->password = $this->hashPassword($this->password);
	}
	
	/**
	 * Generates the password hash.
	 * @param string password
	 * @return string hash
	 */
	public function hashPassword($password)
	{
		return md5($password);
	}
	
	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)
	{
		return $this->hashPassword($password)===$this->password;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
