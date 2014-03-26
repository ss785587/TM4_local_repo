<?php

/**
 * This is the model class for table "testdefinition".
 *
 * The followings are the available columns in table 'testdefinition':
 * @property integer $idTestDefinition
 * @property string $name
 * @property string $version
 * @property integer $probability
 * @property integer $maxNum
 * @property integer $currentNum
 * @property integer $currentNumFinished
 * @property integer $allowServeralTimes
 * @property string $created
 * @property string $startTime
 * @property string $endTime
 * @property integer $active
 * @property integer $uberTestId
 * @property string $testBlueprint
 *
 * The followings are the available model relations:
 * @property Ubertest $uberTest
 * @property TestdefinitionMeta[] $testdefinitionMetas
 * @property Testrun[] $testruns
 */
class TestDefinition extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'testdefinition';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('probability, maxNum, currentNum, currentNumFinished, allowServeralTimes, active, uberTestId', 'numerical', 'integerOnly'=>true),
            array('name, version', 'length', 'max'=>255),
            array('created, startTime, endTime, testBlueprint', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idTestDefinition, name, version, probability, maxNum, currentNum, currentNumFinished, allowServeralTimes, created, startTime, endTime, active, uberTestId, testBlueprint', 'safe', 'on'=>'search'),
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
            'uberTest' => array(self::BELONGS_TO, 'Ubertest', 'uberTestId'),
            'testdefinitionMetas' => array(self::HAS_MANY, 'TestdefinitionMeta', 'testDefinitionId'),
            'testruns' => array(self::HAS_MANY, 'Testrun', 'testDefinitionId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'idTestDefinition' => 'Id Test Definition',
            'name' => 'Name',
            'version' => 'Version',
            'probability' => 'Probability',
            'maxNum' => 'Max Num',
            'currentNum' => 'Current Num',
            'currentNumFinished' => 'Current Num Finished',
            'allowServeralTimes' => 'Allow Serveral Times',
            'created' => 'Created',
            'startTime' => 'Start Time',
            'endTime' => 'End Time',
            'active' => 'Active',
            'uberTestId' => 'Uber Test',
            'testBlueprint' => 'Test Blueprint',
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

        $criteria->compare('idTestDefinition',$this->idTestDefinition);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('version',$this->version,true);
        $criteria->compare('probability',$this->probability);
        $criteria->compare('maxNum',$this->maxNum);
        $criteria->compare('currentNum',$this->currentNum);
        $criteria->compare('currentNumFinished',$this->currentNumFinished);
        $criteria->compare('allowServeralTimes',$this->allowServeralTimes);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('startTime',$this->startTime,true);
        $criteria->compare('endTime',$this->endTime,true);
        $criteria->compare('active',$this->active);
        $criteria->compare('uberTestId',$this->uberTestId);
        $criteria->compare('testBlueprint',$this->testBlueprint,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TestDefinition the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}