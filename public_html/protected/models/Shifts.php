<?php

/**
 * This is the model class for table "shifts".
 *
 * The followings are the available columns in table 'shifts':
 * @property integer $id
 * @property integer $azsId
 * @property integer $number
 * @property string $dateStart
 * @property string $dateStop
 *
 * The followings are the available model relations:
 * @property Azs $azs
 * @property Shifttanks[] $shifttanks
 */
class Shifts extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Shifts the static model class
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
		return 'shifts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('azsId, dateStart', 'required'),
			array('azsId, number', 'numerical', 'integerOnly'=>true),
			array('dateStop', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, azsId, number, dateStart, dateStop', 'safe', 'on'=>'search'),
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
			'azs' => array(self::BELONGS_TO, 'Azs', 'azsId'),
			'shifttanks' => array(self::HAS_MANY, 'Shifttanks', 'shiftId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'azsId' => 'Azs',
			'number' => 'Number',
			'dateStart' => 'Date Start',
			'dateStop' => 'Date Stop',
		);
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

		$criteria->compare('id',$this->id);
		$criteria->compare('azsId',$this->azsId);
		$criteria->compare('number',$this->number);
		$criteria->compare('dateStart',$this->dateStart,true);
		$criteria->compare('dateStop',$this->dateStop,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function change($azsId)
	{
		$connection = Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try {
		$sql = 'call changeShift(:in_azsId)';
		$command = $connection->createCommand($sql);
		$command->bindValues(array (
          'in_azsId'=>$azsId
		));
		$command->execute();
		$transaction->commit();
		} catch(Exception $e) {
			$transaction->rollBack();
			throw $e;
		}	
		return true;		 	
	}
	
}