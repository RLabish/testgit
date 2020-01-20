<?php

/**
 * This is the model class for table "pumpcounters".
 *
 * The followings are the available columns in table 'pumpcounters':
 * @property integer $id
 * @property integer $pumpId
 * @property string $date
 * @property string $counter
 *
 * The followings are the available model relations:
 * @property Pumps $pump
 */
class PumpCounters extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PumpCounters the static model class
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
		return 'pumpcounters';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pumpId', 'required'),
			array('pumpId', 'numerical', 'integerOnly'=>true),
			array('counter', 'length', 'max'=>10),
			array('date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, pumpId, date, counter', 'safe', 'on'=>'search'),
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
			'pump' => array(self::BELONGS_TO, 'Pumps', 'pumpId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pumpId' => 'Pump',
			'date' => 'Date',
			'counter' => 'Counter',
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
		$criteria->compare('pumpId',$this->pumpId);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('counter',$this->counter,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
}