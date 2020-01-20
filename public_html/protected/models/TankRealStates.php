<?php

/**
 * This is the model class for table "tankrealstates".
 *
 * The followings are the available columns in table 'tankrealstates':
 * @property integer $id
 * @property integer $tankId
 * @property integer $fuelId
 * @property string $date
 * @property integer $status
 * @property string $fuelLevel
 * @property string $fuelVolume
 * @property string $fuelMass
 * @property string $temperature
 * @property string $density
 * @property string $waterLevel
 * @property string $waterVolume
 *
 * The followings are the available model relations:
 * @property Fuels $fuel
 * @property Tanks $tank
 * @property Tanks[] $tanks
 */
class TankRealStates extends CActiveRecord
{
	public $dateFrom;
	public $dateTo;	
		
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tankrealstates';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tankId, date, status', 'required'),
			array('tankId, fuelId, status', 'numerical', 'integerOnly'=>true),
			array('id, tankId, fuelId, date, dateFrom, dateTo, status, fuelLevel, fuelVolume, fuelMass, temperature, density, waterLevel, waterVolume', 'safe', 'on'=>'search'),
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
			'tank' => array(self::BELONGS_TO, 'Tanks', 'tankId'),
			'tanks' => array(self::HAS_MANY, 'Tanks', 'realStateId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tankId' => 'Tank',
			'fuelId' => 'Fuel',
			'date' => 'Date',
			'status' => 'Status',
			'fuelLevel' => 'Fuel Level',
			'fuelVolume' => 'Fuel Volume',
			'fuelMass' => 'Fuel Mass',
			'temperature' => 'Temperature',
			'density' => 'Density',
			'waterLevel' => 'Water Level',
			'waterVolume' => 'Water Volume',
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
		$criteria->compare('tankId',$this->tankId);
		$criteria->compare('fuelId',$this->fuelId);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('status',$this->status);

		if (isset($this->dateFrom) && ($this->dateFrom != '') ){
			$from = date("Y-m-d", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->dateFrom)));
			$criteria->compare('cast(t.date as date)',">= $from",true);
		}
		if (isset($this->dateTo) && ($this->dateTo != '') ){
			$to = date("Y-m-d", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->dateTo)));
			$criteria->compare('cast(t.date as date)',"<= $to",true);
		}
		$criteria->order = 'date';		

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}