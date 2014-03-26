<?php

/**
 * This is the model class for table "ubertest".
 *
 * The followings are the available columns in table 'ubertest':
 * @property integer $idUberTest
 * @property string $name
 * @property string $niceName
 * @property string $descriptionShort
 * @property string $descriptionLong
 * @property string $mediaURL
 *
 * The followings are the available model relations:
 * @property Tan[] $tans
 * @property TestdefTokenRel[] $testdefTokenRels
 * @property Testdefinition[] $testdefinitions
 * @property UbertestGroupRel[] $ubertestGroupRels
 * @property UbertestMeta[] $ubertestMetas
 * @property UbertestTermsRel[] $ubertestTermsRels
 */
class UberTest extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ubertest';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, niceName, mediaURL', 'length', 'max'=>255),
			array('descriptionShort, descriptionLong', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idUberTest, name, niceName, descriptionShort, descriptionLong, mediaURL', 'safe', 'on'=>'search'),
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
			'tans' => array(self::HAS_MANY, 'Tan', 'uberTestId'),
			'testdefTokenRels' => array(self::HAS_MANY, 'TestdefTokenRel', 'uberTest_id'),
			'testdefinitions' => array(self::HAS_MANY, 'Testdefinition', 'uberTestId'),
			'ubertestGroupRels' => array(self::HAS_MANY, 'UbertestGroupRel', 'uberTestId'),
			'ubertestMetas' => array(self::HAS_MANY, 'UbertestMeta', 'uberTestId'),
			'ubertestTermsRels' => array(self::HAS_MANY, 'UbertestTermsRel', 'uberTestId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idUberTest' => 'Id Uber Test',
			'name' => 'Name',
			'niceName' => 'Nice Name',
			'descriptionShort' => 'Description Short',
			'descriptionLong' => 'Description Long',
			'mediaURL' => 'Media Url',
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

		$criteria->compare('idUberTest',$this->idUberTest);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('niceName',$this->niceName,true);
		$criteria->compare('descriptionShort',$this->descriptionShort,true);
		$criteria->compare('descriptionLong',$this->descriptionLong,true);
		$criteria->compare('mediaURL',$this->mediaURL,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UberTest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
