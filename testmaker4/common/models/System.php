<?php

/**
 * This is the model class for table "system".
 *
 * The followings are the available columns in table 'system':
 * @property integer $idSystem
 * @property string $settingName
 * @property string $settingValue
 */
class System extends CActiveRecord
{
	//database key names
	const FRONTEND_LANGUAGE_DEFAULT = "tm.frontend.language.default";
	const FRONTEND_LANGUAGE_ACTIVE = "tm.frontend.language.active";
	const DATAPRIVACY_CURRENT_VERSION = "system.dataprivacy.currentstatementversion ";
	

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'system';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('settingName, settingValue', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idSystem, settingName, settingValue', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idSystem' => 'Id System',
			'settingName' => 'Setting Name',
			'settingValue' => 'Setting Value',
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

		$criteria->compare('idSystem',$this->idSystem);
		$criteria->compare('settingName',$this->settingName,true);
		$criteria->compare('settingValue',$this->settingValue,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Reads system preferences form table SYSTEM in database.
	 *
	 * @param  $key should be a constant, defined in this class
	 * @throws CDbException - thrown if the key is invalid
	 */
	public static function getValue($key){
		$systemSetting = System::model()->find('settingName=:value', array(':value'=>$key));
		if(isset($systemSetting)){
			return $value = $systemSetting->settingValue;
		}
		throw new CDbException('Invalid system preference key: '.$key);
	}
	

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return System the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
