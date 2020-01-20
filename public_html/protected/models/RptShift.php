<?php

Yii::import('application.extensions.MyUtils'); 

class RptSales extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CardOperations the static model class
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
		return 'cardoperations';
	}

	public $dateFrom;
	public $dateTo;
	public $terminalId;
	public $azsId;
	public $terminal;
	public $driverName;
	public $autoName;
	public $fuelName;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cardId, fuelId, volume', 'required'),
			array('operationType, cardId, fuelId', 'numerical', 'integerOnly'=>true),
			array('volume', 'numerical', 'integerOnly'=>false),
			
			array('dateFrom, dateTo, cardOwner, cardNumber', 'safe'),
			array('dateFrom, dateTo, driverName, autoName, terminalId, azsId, cardId, fuelId, operation, volume, balance', 'safe', 'on'=>'search'),
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
			'card' => array(self::BELONGS_TO, 'Cards', 'cardId'),
			'card2' => array(self::BELONGS_TO, 'Cards', 'card2Id'),
			'fuel' => array(self::BELONGS_TO, 'Fuels', 'fuelId'),
			'cardrefill' => array(self::HAS_ONE, 'CardRefill', 'operId'),
			'pumptransaction' => array(self::HAS_ONE, 'PumpTransactions', 'operId'),
								
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'operationType' => 'Операция',
			'date' => 'Дата',
			'cardId' => 'Card',
			'fuelId' => 'Вид топлива',
			'description' => 'Операция',
			'volume' => 'Объем, л',
			'balance' => 'Остаток, л',
			'refillDescription' => 'Основание'
		);
	}	

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('operationType',2);
		if (!isset($this->dateFrom) || ($this->dateFrom == ''))
			$this->dateFrom = MyUtils::datetimeFormat('dd.MM.yyyy');
		$criteria->compare('date',">=".MyUtils::datetimeFormat('y-M-d 0:0:0', $this->dateFrom));
		if (!isset($this->dateTo) || ($this->dateTo == ''))
			$this->dateTo = MyUtils::datetimeFormat('dd.MM.yyyy');
		$criteria->compare('date',"<=".MyUtils::datetimeFormat('y-M-d 23:59:59', $this->dateTo));
		
		$criteria->compare('driver.name', $this->driverName, true);
		$criteria->compare('auto.number', $this->autoName, true);
		$criteria->compare('terminal.id', $this->terminalId);
		$criteria->compare('azs.id', $this->azsId);
		
		$criteria->select = '*';
		$criteria->order = 'date';
		$criteria->with = array(
				'pumptransaction.terminal' => array(
						'select' => 'id, name',
				),
				'card.driver' => array(
						'select' => 'name',
				),
				'card2.auto' => array(
						'select' => 'name',
				),
				'fuel' => array(
						'alias' => 'f',
						'select' => 'name',
						'together' => true,
				),
		);
		$criteria->with = array('card', 'card.driver', 'card2', 'card2.auto', 'fuel', 'pumptransaction', 'pumptransaction.terminal', 'pumptransaction.tank', 'pumptransaction.tank.azs');	

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize'=>50,
			),				
			'sort' => array(
      			'defaultOrder' => 'date asc',
   			),
		));
	}
		
	protected function afterFind() {
		parent::afterFind();
		$this->volume = -$this->volume;
		if (isset($this->card->driver))
			$this->driverName = $this->card->driver->name;
		if (isset($this->card2->auto))
			$this->autoName = $this->card2->auto->name;	
		$this->terminal = $this->pumptransaction->terminal;	
		return;
	}
}