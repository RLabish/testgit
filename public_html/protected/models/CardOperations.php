<?php

/**
 * This is the model class for table "zterm_cardoperations".
 *
 * The followings are the available columns in table 'zterm_cardoperations':
 * @property integer $id
 * @property integer $operationType
 * @property string $date
 * @property integer $cardId
 * @property integer $fuelId
 * @property string $volume
 * @property string $balance
 *
 * The followings are the available model relations:
 * @property Cards $card
 * @property Fuels $fuel
 */
class CardOperations extends CActiveRecord
{
	const TYPE_CARD_REFILL = 1;
	const TYPE_PUMP_SALE = 2;
	const TYPE_PUMP_ALARM = 3;
	const TYPE_PUMP_SERVICE = 4;
	const TYPE_PUMP_MOVE = 5;
	const TYPE_AUTO_CALIBR = 6;	
	const TYPE_CLIENT_REFILL = 10;		
	
	
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

	public $fuelName;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fuelId, volume', 'required'),
			array('operationType, cardId, fuelId', 'numerical', 'integerOnly'=>true),
			array('volume', 'numerical', 'integerOnly'=>false),
			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('operationType, refillDescription, cardOwner, cardNumber, cardDescription, organizationName', 'safe'),
			array('cardId, fuelId, operation, volume, balance, date_range', 'safe', 'on'=>'search'),
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
	public function search()
	{
		$criteria=new CDbCriteria;		
		//date_range
		if (isset($this->date_range['from'])){
			$dt = $this->date_range['from'];
			if ($dt != '') {
				$from = date("Y-m-d 00:00:00", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $dt)));
				$criteria->compare('date',">= $from",true);
			}
		}
		if (isset($this->date_range['to'])){
			$dt = $this->date_range['to'];
			if ($dt != '') {
				$to = date("Y-m-d 23:59:59", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $dt)));
				$criteria->compare('date',"<= $to",true);
			}
		}		
		$criteria->compare('id',$this->id);
		if (isset($this->operationType)) {
			$arr = explode('.', $this->operationType);		
			$criteria->compare('operationType',$arr[0]);
			if (isset($arr[1])) {
				$criteria->compare('terminal.id',$arr[1]);				
			}
		
		}		
//		$criteria->compare('date',$this->date,true);
		$criteria->compare('cardId',$this->cardId);
		$criteria->compare('t.fuelId',$this->fuelId);
		$criteria->compare('volume',$this->volume,true);
		$criteria->compare('balance',$this->balance,true);		
		$criteria->with = array('card', 'autoCard', 'fuel', 'pumptransaction', 'pumptransaction.terminal', 'pumptransaction.tank', 'cardrefill');	
//$criteria->with = array('card', 'autoCard', 'fuel', 'pumptransaction', 'pumptransaction.terminal', 'pumptransaction.tank', );			
		if (Yii::app()->theme->name == 'clients') {
			$criteria->with = array_merge($criteria->with, array('organization'));	
		}
		
		//cardNumber, cardOwner
		if (!isset($this->cardNumber))
			$this->cardNumber = '';
		else
			$this->cardNumber = trim($this->cardNumber);
		
		if (!isset($this->cardOwner))
			$this->cardOwner = '';
		else
			$this->cardOwner = trim($this->cardOwner);
		if (!isset($this->cardDescription))
			$this->cardDescription = '';
		else
			$this->cardDescription = trim($this->cardDescription);
			
		if (!isset($this->organizationName))
			$this->organizationName = '';
		else
			$this->organizationName = trim($this->organizationName);

		if (($this->cardNumber != '') || ($this->cardOwner != '') || ($this->cardDescription != '') || ($this->organizationName != '')){
        	if ($this->cardNumber != '') 
        		$criteria->compare('card.number', $this->cardNumber, false);
        	if ($this->organizationName != '') 
        		$criteria->compare('organization.name', $this->organizationName, true);
				
				
        	if ($this->cardOwner != '') { 
				if ($criteria->condition != '')
					$criteria->condition = $criteria->condition.' and ';
				$s = str_replace(' ', '%', $this->cardOwner);
				$s = str_replace(array('"', "'"), '', $s);
				$criteria->condition = $criteria->condition.'( card.owner like "%'.$s.'%"'.' or autoCard.owner like "%'.$s.'%")';
			}			
			
        	if ($this->cardDescription != '') { 
				if ($criteria->condition != '')
					$criteria->condition = $criteria->condition.' and ';
				$s = str_replace(' ', '%', $this->cardDescription);
				$s = str_replace(array('"', "'"), '', $s);
				$criteria->condition = $criteria->condition.'( card.description like "%'.$s.'%"'.' or autoCard.description like "%'.$s.'%")';
			}			
			
		}	

		
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize'=>50,
			),				
			'sort' => array(
      			'defaultOrder' => 'date DESC',
   			),
		));
	}
	public $organizationName; 
	public $refillDescription;
	public $date_range = array();
	public $cardNumber = '';
	public $cardOwner = '';
	public $cardDescription = '';
	public $terminalName = '';
	public $description = '';
	
	protected function afterFind() {
		parent::afterFind();

		if ($this->operationType == CardOperations::TYPE_CARD_REFILL) {
			if ($this->volume >= 0) 
				$this->description = 'Пополнение карты';
			else
				$this->description = 'Списание с карты';
			if (isset($this->cardrefill) && isset($this->cardrefill->document) && ($this->cardrefill->document != ''))
				$this->description = $this->description . ' (' . $this->cardrefill->document . ')';


			
		}
		else if (($this->operationType == CardOperations::TYPE_PUMP_SALE) 
				|| ($this->operationType == CardOperations::TYPE_PUMP_ALARM)
				|| ($this->operationType == CardOperations::TYPE_PUMP_SERVICE)				
				|| ($this->operationType == CardOperations::TYPE_PUMP_MOVE)		
				|| ($this->operationType == CardOperations::TYPE_AUTO_CALIBR)		
				){
			$s = isset($this->pumptransaction) ? $this->pumptransaction->terminal->name : '???';
			if ($this->operationType == CardOperations::TYPE_PUMP_SALE)
				$this->description = 'Выдача топлива ('.$s.')';
			else if ($this->operationType == CardOperations::TYPE_PUMP_ALARM)
				$this->description = 'Автономный пролив ('.$s.')';
			else if ($this->operationType == CardOperations::TYPE_PUMP_SERVICE)
				$this->description = 'Техпролив ('.$s.')';
			else if ($this->operationType == CardOperations::TYPE_PUMP_MOVE)
				$this->description = 'Перемещение ('.$s.')';
		}
		
		else if ($this->operationType == CardOperations::TYPE_CLIENT_REFILL) {
			$this->description = 'Пополнение клиента';			
			$this->cardId = null;
			unset($this->card);			
		}
		
		if ((Yii::app()->theme->name == 'clients') && isset($this->orgId)) 
			$this->organizationName = $this->organization->name;	
		
		if (isset($this->card)) { 
			$this->cardOwner = $this->card->owner;	
			$this->cardDescription = $this->card->description;	
		}
	}
}
