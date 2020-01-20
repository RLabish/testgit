<?php

/**
 * This is the model class for table "tanks".
 *
 * The followings are the available columns in table 'tanks':
 * @property integer $id
 * @property integer $terminalId
 * @property integer $fuelId
 * @property integer $number
 * @property string $name
 * @property string $height
 * @property string $capacity
 * @property string $minLevel
 * @property string $maxLevel
 * @property integer $realStateId
 *
 * The followings are the available model relations:
 * @property Tankbookstates[] $tankbookstates
 * @property TankRealStates[] $tankrealstates
 * @property Terminals $terminal
 * @property Fuels $fuel
 * @property TankRealStates $realState
 */
class Tanks extends CActiveRecord
{
	public $azsName;
	public $terminalName;
	
	public $realFuelLevel;
	public $realFuelVolume;
	public $realTemperature;
	public $realDensity;
	public $realFuelMass;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Tanks the static model class
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
		return 'tanks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('fuelId, azsId, name, capacity', 'required', 'on'=>'search'),
			array('terminalId, fuelId, number, realStateId', 'numerical', 'integerOnly'=>true),
			array('azsId, fuelId, capacity, name, number, maxVolume, visible', 'required'),
			array('terminalId', 'safe'),
			array('name', 'length', 'max'=>30),		
			array('id, terminalId, fuelId, number, name, height, capacity, minVolume, maxVolume, realStateId', 'safe', 'on'=>'search'),
			array('id, realFuelLevel, realFuelVolume, realTemperature, realDensity, realFuelMass', 'required', 'on'=>'updateRealState'),
			array('realFuelLevel, realFuelVolume, realTemperature, realFuelMass', 'numerical', 'on'=>'updateRealState'),
			array('realDensity', 'numerical', 'min' => 0.6, 'max' => 0.9, 'on'=>'updateRealState'),
			
		);
	}
	

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'terminal' => array(self::BELONGS_TO, 'Terminals', 'terminalId'),
			'azs' => array(self::BELONGS_TO, 'Azs', 'azsId'),
			'fuel' => array(self::BELONGS_TO, 'Fuels', 'fuelId'),
			'realState' => array(self::BELONGS_TO, 'TankRealStates', 'realStateId'),
			'bookState' => array(self::BELONGS_TO, 'TankBookStates', 'bookStateId'),
			'bookStates' => array(self::HAS_MANY, 'TankBookStates', 'tankId'),				
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Резервуар',
			'terminalId' => 'Терминал',
			'terminalName' => 'Терминал',
			'azsId' => 'АЗС',
			'azsName' => 'АЗС',
			'note' => 'Примечание',			
			'fuelId' => 'Топливо',
			'number' => 'Номер',
			'name' => 'Наименование',
			'height' => 'Высота',
			'capacity' => 'Вместимость,л',
			'minVolume' => 'Мин. объем,л',
			'maxVolume' => 'Макс. объем,л',
			'visible' => 'Уровнемер',
			'realStateId' => 'Real State',		
			'realFuelLevel' => 'Уровень,мм',
			'realFuelVolume' => 'Объем,л',
			'realTemperature' => 'Температура,*С',
			'realDensity' => 'Плотность,г/cм3',
			'realFuelMass' => 'Масса,кг',
		);
	}


	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->with = array('azs','fuel', 'realState');
		$criteria->order = 'azs.name, t.number';

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize'=>100,
			),
		));
	}
	
	public function afterFind()
	{
		parent::afterFind();
		$this->azsName = $this->azs->name;
		if (isset($this->terminal)) $this->terminalName = $this->terminal->name;
		if (isset($this->realState)) {
			$this->realFuelLevel = $this->realState->fuelLevel;
			$this->realFuelVolume = $this->realState->fuelVolume;
			$this->realTemperature = $this->realState->temperature;
			$this->realDensity = $this->realState->density;
			$this->realFuelMass = $this->realState->fuelMass;
		}
	}
	
	public function beforeSave()
	{
		if (!isset($this->maxVolume))
			$this->maxVolume = $this->capacity;
		if (!isset($this->minVolume))
			$this->minVolume = 0;
		if (!isset($this->height))
			$this->height = 3000;		
		return parent::beforeSave();
	}

	public static function listData($type, $withNull = false)
	{
		$criteria=new CDbCriteria;
		$criteria->with = array('fuel', 'azs');
		$criteria->compare('visible', '>0');
					
		if ($type == 'azs')
			$criteria->order = 'azs.name, t.name';
				
		if ($withNull) 
			$res = array('' => '');			
		else
			$res = array();
		foreach (Tanks::model()->findAll($criteria) as $tank) {
			if ($type == 'azs')
				$res[$tank->id] = $tank->azs->name.'-'.$tank->name.'-'.$tank->fuel->name;
		}
		return $res;
	}	
	
	public function fullName($template) {
		//'azs,name,fuel'
		$res = '';
		foreach (explode(',', $template) as $s) {
			$v = '';
			if ($s == 'azs')
				$v = $this->azs->name;
			else if ($s == 'name')
				$v = $this->name;
			else if ($s == 'fuel')
				$v = $this->fuel->name;
			if ($v != '')
				$res = $res.'-'.$v;
		}
		return substr($res, 1);
	}
		
	public function levelToVolume($level) {
		$tblfilename = YiiBase::getPathOfAlias('webroot.protected.data.tank'.$this->id).'.tbl';
		$level1 = (int)(floor($level / 10) * 10);
		$level2 = $level1 + 10;
		$volume1 = 0;
		$volume2 = 0;
		
		$hfile = fopen($tblfilename, "r");
		for ($i = 1; $i < ($level1 / 10); $i++)
			fgets($hfile);
		if (($buffer = fgets($hfile)) !== false) {
			$volume1 = (int)$buffer;		
		}
		if (($buffer = fgets($hfile)) !== false) {
			$volume2 = (int)$buffer;		
		}
		fclose($hfile); 
		return round($volume1 + ($volume2 - $volume1)/10 * ($level - $level1));
	}
		
	public function saveRealState() {
		$connection = Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try {
		$sql = 'call tankSetRealState(:in_tankId, null, :in_status, :in_fuelLevel, :in_fuelVolume, :in_fuelMass, :in_temperature, :in_density, :in_waterLevel, :in_waterVolume)';
		$command = $connection->createCommand($sql);
		$command->bindValues(array (
          'in_tankId'=>$this->id,
          'in_status'=> 0,
          'in_fuelLevel'=> $this->realFuelLevel,
          'in_fuelVolume'=> $this->realFuelVolume,
          'in_fuelMass'=> $this->realFuelMass,
          'in_temperature'=> $this->realTemperature,
          'in_density'=> $this->realDensity,
          'in_waterLevel'=> 0,
          'in_waterVolume'=> 0
		));
		$command->execute();
		$transaction->commit();
		} catch(Exception $e) {
			$transaction->rollBack();
			throw $e;
		}	
		return true;	
	/*
		if($this->validate()) {
			$realState = new TankRealStates();
			$realState->isNewRecord = true;
			$realState->id = null;
			$realState->tankId = $this->id;
			$realState->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());
			$realState->status = 0;
			$realState->fuelLevel = $this->realFuelLevel;
			$realState->fuelVolume =  $this->realFuelVolume;
			$realState->temperature =  $this->realTemperature;
			$realState->density = $this->realDensity;
			$realState->fuelMass =  $this->realFuelMass;
			$realState->waterLevel =  0;
			$realState->waterVolume = 0;
			if (!$realState->save()) 
				throw new Exception($realState->getErrors());
			$realState->id = Yii::app()->db->lastInsertID;
			$mm = New Tanks();
		  	$tank = $mm->findByPk($this->id);
			$tank->realStateId = $realState->id;
			if (!$tank->save()) 
				throw new Exception($tank->getErrors());
			return true;		
		}
		else
			return false;
*/			
	}	
	
}