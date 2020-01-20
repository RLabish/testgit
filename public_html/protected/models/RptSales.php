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
	public $terminalName;
	public $organizationName;
	public $driverName;
	public $autoName;
	public $cardDescription;
	public $azs;
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
			
			array('dateFrom, dateTo, cardOwner, cardNumber, cardDescription', 'safe'),
			array('dateFrom, dateTo, organizationName, driverName, autoName, terminalId, azsId, cardId, fuelId, operation, volume, balance', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return  array_merge(
			array(
				'card' => array(self::BELONGS_TO, 'Cards', 'cardId'),
				'autoCard' => array(self::BELONGS_TO, 'Cards', 'autoCardId'),
				'fuel' => array(self::BELONGS_TO, 'Fuels', 'fuelId'),
				'cardrefill' => array(self::HAS_ONE, 'CardRefill', 'operId'),
				'pumptransaction' => array(self::HAS_ONE, 'PumpTransactions', 'operId'),
			),
			
			(Yii::app()->theme->name == 'clients') ? array(
				'organization' => array(self::BELONGS_TO, 'Organizations', 'orgId'),
			): array()			
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
			'refillDescription' => 'Основание',
			'organizationName' => (Yii::app()->theme->name == 'clients')?'Клиент':'Организация',
		);
	}	

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function searchCriteria()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('operationType',2);
		if (!isset($this->dateFrom) || ($this->dateFrom == ''))
			$this->dateFrom = MyUtils::datetimeFormat('dd.MM.yyyy');
		$criteria->compare('date',">=".MyUtils::datetimeFormat('y-M-d 0:0:0', $this->dateFrom));
		if (!isset($this->dateTo) || ($this->dateTo == ''))
			$this->dateTo = MyUtils::datetimeFormat('dd.MM.yyyy');
		$criteria->compare('date',"<=".MyUtils::datetimeFormat('y-M-d 23:59:59', $this->dateTo));
		
		$this->driverName = trim($this->driverName);
		$criteria->compare('card.owner', $this->driverName, true);
		$this->cardDescription = trim($this->cardDescription);
		$criteria->compare('card.description', $this->cardDescription, true);

		$this->autoName = trim($this->autoName);
		$criteria->compare('autoCard.owner', $this->autoName, true);
		$criteria->compare('terminal.id', $this->terminalId);
		$criteria->compare('azs.id', $this->azsId);
		
		
		$criteria->select = '*';
		$criteria->order = 'date';
		$criteria->with = array('card', 'autoCard', 'fuel', 'pumptransaction', 'pumptransaction.terminal.azs');	
		
		if 	(Yii::app()->theme->name == 'clients') {
			$criteria->compare('organization.name', $this->organizationName, true);
			$criteria->with = array_merge($criteria->with, array('card.organization'));
		}	
        return $criteria;		
	}
		 
	public function search()
	{
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$this->searchCriteria(),
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
		if (isset($this->orgId)) $this->organizationName = $this->organization->name;					
		if (isset($this->card->owner)) $this->driverName = $this->card->owner;
		if (isset($this->pumptransaction))	$this->terminal = $this->pumptransaction->terminal;	
		if (isset($this->terminal))	$this->terminalName = $this->terminal->name;
		
	}
	
	public function getFuelTotals($fuelId)
	{
		$criteria = $this->searchCriteria();
		$criteria->select = 'SUM(volume) as volume';
		$criteria->compare('t.fuelId',$fuelId);
		$res = $this->find($criteria);
		if (isset($res))
			return $res->volume;
		return 0;
	}	
}