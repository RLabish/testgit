<?php

/**
 * This is the model class for table "shifttanks".
 *
 * The followings are the available columns in table 'shifttanks':
 * @property integer $id
 * @property integer $shiftId
 * @property integer $tankId
 * @property integer $fuelId
 * @property string $realStartFuelVolume
 * @property string $realStopFuelVolume
 * @property string $bookStartFuelVolume
 * @property string $bookStopFuelVolume
 * @property string $incomeVolume
 * @property string $moveInVolume
 * @property string $moveOutVolume
 * @property string $saleVolume
 * @property string $serviceVolume
 *
 * The followings are the available model relations:
 * @property Fuels $fuel
 * @property Shifts $shift
 * @property Tanks $tank
 */
class ShiftTanks extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ShiftTanks the static model class
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
		return 'shifttanks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('shiftId, tankId, fuelId', 'required'),
			array('shiftId, tankId, fuelId', 'numerical', 'integerOnly'=>true),
			array('realStartFuelVolume, realStopFuelVolume, bookStartFuelVolume, bookStopFuelVolume, incomeVolume, moveInVolume, moveOutVolume, saleVolume, serviceVolume', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, shiftId, tankId, fuelId, realStartFuelVolume, realStopFuelVolume, bookStartFuelVolume, bookStopFuelVolume, incomeVolume, moveInVolume, moveOutVolume, saleVolume, serviceVolume', 'safe', 'on'=>'search'),
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
			'fuel' => array(self::BELONGS_TO, 'Fuels', 'fuelId'),
			'shift' => array(self::BELONGS_TO, 'Shifts', 'shiftId'),
			'tank' => array(self::BELONGS_TO, 'Tanks', 'tankId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'shiftId' => 'Shift',
			'tankId' => 'Tank',
			'fuelId' => 'Fuel',
			'realStartFuelVolume' => 'Real Start Fuel Volume',
			'realStopFuelVolume' => 'Real Stop Fuel Volume',
			'bookStartFuelVolume' => 'Book Start Fuel Volume',
			'bookStopFuelVolume' => 'Book Stop Fuel Volume',
			'incomeVolume' => 'Income Volume',
			'moveInVolume' => 'Move In Volume',
			'moveOutVolume' => 'Move Out Volume',
			'saleVolume' => 'Sale Volume',
			'serviceVolume' => 'Service Volume',
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
		$criteria->compare('shiftId',$this->shiftId);
		$criteria->compare('tankId',$this->tankId);
		$criteria->compare('fuelId',$this->fuelId);
		$criteria->compare('realStartFuelVolume',$this->realStartFuelVolume,true);
		$criteria->compare('realStopFuelVolume',$this->realStopFuelVolume,true);
		$criteria->compare('bookStartFuelVolume',$this->bookStartFuelVolume,true);
		$criteria->compare('bookStopFuelVolume',$this->bookStopFuelVolume,true);
		$criteria->compare('incomeVolume',$this->incomeVolume,true);
		$criteria->compare('moveInVolume',$this->moveInVolume,true);
		$criteria->compare('moveOutVolume',$this->moveOutVolume,true);
		$criteria->compare('saleVolume',$this->saleVolume,true);
		$criteria->compare('serviceVolume',$this->serviceVolume,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}