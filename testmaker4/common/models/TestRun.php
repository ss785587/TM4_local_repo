<?php

/**
 * This is the model class for table "testrun".
 *
 * The followings are the available columns in table 'testrun':
 * @property integer $idTestRun
 * @property string $jsonData
 * @property integer $testDefinitionId
 * @property integer $userId
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property Testdefinition $testDefinition
 * @property User $user
 * @property Testruneval[] $testrunevals
 */
class TestRun extends CActiveRecord
{
	
	/** different values of status */
	const STATUS_NEW = 0;
	const STATUS_STARTED = 1;
	const STATUS_PAUSED = 2;
	const STATUS_CONTINUED = 3;
	const STATUS_COMPLETED = 4;
	const STATUS_SCREENED_OUT = 5;
	const STATUS_QUOTA_ACCOMPLISHED = 6;
		
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'testrun';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('testDefinitionId, userId', 'numerical', 'integerOnly'=>true),
			array('jsonData', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idTestRun, jsonData, testDefinitionId, userId, status', 'safe', 'on'=>'search'),
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
			'testDefinition' => array(self::BELONGS_TO, 'Testdefinition', 'testDefinitionId'),
			'user' => array(self::BELONGS_TO, 'User', 'userId'),
			'testrunevals' => array(self::HAS_MANY, 'Testruneval', 'testRundId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idTestRun' => 'Id Test Run',
			'jsonData' => 'Json Data',
			'testDefinitionId' => 'Test Definition',
			'userId' => 'User',
			'stauts' => 'Status',
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

		$criteria->compare('idTestRun',$this->idTestRun);
		$criteria->compare('jsonData',$this->jsonData,true);
		$criteria->compare('testDefinitionId',$this->testDefinitionId);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TestRun the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
