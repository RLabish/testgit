<?php

/**
 * This is the model class for table "tankbookstates".
 *
 * The followings are the available columns in table 'tankbookstates':
 * @property integer $id
 * @property string $date
 * @property integer $tankId
 * @property integer $fuelId
 * @property string $incomeVolume
 * @property string $moveInVolume
 * @property string $moveOutVolume
 * @property string $saleVolume
 * @property string $fuelVolume
 *
 * The followings are the available model relations:
 * @property Tanks $tank
 */
class TankBookStates extends CActiveRecord
{
	public $dateFrom;
	public $dateTo;	
	public $azsId;	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TankBookStates the static model class
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
		return 'tankbookstates';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, tankId, fuelId', 'required'),
			array('tankId, fuelId', 'numerical', 'integerOnly'=>true),
			array('incomeVolume, moveInVolume, moveOutVolume, saleVolume, fuelVolume', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, dateFrom, dateTo, tankId, fuelId, azsId,incomeVolume, moveInVolume, moveOutVolume, saleVolume, fuelVolume', 'safe', 'on'=>'search'),
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
			'tank' => array(self::BELONGS_TO, 'Tanks', 'tankId'),
			'fuel' => array(self::BELONGS_TO, 'Fuels', 'fuelId'),
			'tankIncome' => array(self::HAS_MANY, 'TankIncome', array('date'=>'date', 'tankId' => 'tankId')),				
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'date' => 'Date',
			'tankId' => 'Tank',
			'fuelId' => 'Fuel',
			'incomeVolume' => 'Income Volume',
			'moveInVolume' => 'Move In Volume',
			'moveOutVolume' => 'Move Out Volume',
			'saleVolume' => 'Sale Volume',
			'fuelVolume' => 'Fuel Volume',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->condition = '1=1';
		$criteria->with = array('tank');
		if (isset($this->dateFrom) && ($this->dateFrom != '') ){
			$from = date("Y-m-d", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->dateFrom)));
			$criteria->compare('t.date',">= $from",true);
		}
		if (isset($this->dateTo) && ($this->dateTo != '') ){
			$to = date("Y-m-d", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->dateTo)));
			$criteria->compare('t.date',"<= $to",true);
		}

		if (isset($this->depId) || isset($this->azsId)) {			
			$criteria->with[] = 'tank.azs'; 
			$criteria->compare('azs.id', $this->azsId);
		}
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => array(
				'defaultOrder' => 't.date asc',
			),				
		));
	}
	
	private $_tankMoveFrom = null;
	public function tankMoveFrom() {
		if ($this->_tankMoveFrom) return $this->_tankMoveFrom;
		$criteria = new CDbCriteria();
		$criteria->compare('cast(t.date as date)', $this->date);
		$criteria->compare('t.tankFromId', $this->tankId);
		$x = new TankMove();
		$this->_tankMoveFrom = $x->findall($criteria);		
		return $this->_tankMoveFrom;
	}
	private $_tankMoveTo = null;
	public function tankMoveTo() {
		if ($this->_tankMoveTo) return $this->_tankMoveTo;
		$criteria = new CDbCriteria();
		$criteria->compare('cast(t.date as date)', $this->date);
		$criteria->compare('t.tankToId', $this->tankId);
		$x = new TankMove();
		$this->_tankMoveTo = $x->findall($criteria);		
		return $this->_tankMoveTo;	}




	
}
