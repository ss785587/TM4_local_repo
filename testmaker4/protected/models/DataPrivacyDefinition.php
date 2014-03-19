<?php

/**
 * This is the model class for table "dataprivacydefinition".
 *
 * The followings are the available columns in table 'dataprivacydefinition':
 * @property integer $id
 * @property string $created
 * @property integer $created_by
 *
 * The followings are the available model relations:
 * @property Dataprivacystatement[] $dataprivacystatements
 */
class DataPrivacyDefinition extends CActiveRecord
{
	public $maxColumn;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dataprivacydefinition';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_by', 'numerical', 'integerOnly'=>true),
			array('created', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, created, created_by', 'safe', 'on'=>'search'),
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
			'dataprivacystatements' => array(self::HAS_MANY, 'Dataprivacystatement', 'version'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created' => 'Created',
			'created_by' => 'Created By',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DataPrivacyDefinition the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
