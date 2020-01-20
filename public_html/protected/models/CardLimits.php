<?php

/**
 * This is the model class for table "cardlimits".
 *
 * The followings are the available columns in table 'cardlimits':
 * @property integer $id
 * @property integer $cardId
 * @property integer $limitType
 * @property integer $fuelId
 * @property string $orderVolume
 * @property string $usageVolume
 * @property string $lastSaleDate
 */
class CardLimits extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CardLimits the static model class
	 */
	const TYPE_SUMMARY = 1;
	const TYPE_ONE = 2;
	const TYPE_DAY = 3;
	const TYPE_MONTH = 4;
	
	private $old;
	
	public $usageVolume;
	public $lastSaleDate;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cardlimits';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cardId, limitType, fuelId, orderVolume', 'required'),
			array('cardId, limitType, fuelId', 'numerical', 'integerOnly'=>true),
			array('orderVolume', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cardId, limitType, fuelId, orderVolume', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'card' => array(self::BELONGS_TO, 'Cards', 'cardId'),
			'fuel' => array(self::BELONGS_TO, 'Fuels', 'fuelId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cardId' => 'Card',
			'limitType' => 'Тип лимита',
			'fuelId' => 'Продукт',
			'orderVolume' => 'Объем, л',
			'usageVolume' => 'Usage Volume',
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
		$criteria->compare('cardId', $_GET['cardId']);
		$criteria->with = array('card', 'fuel');
		$criteria->order = 'fuel.code, limitType';
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public $limitTypeNames 	= array(
			CardLimits::TYPE_SUMMARY => 'суммарный',
			CardLimits::TYPE_ONE => 'разовый',
			CardLimits::TYPE_DAY => 'дневной',
			CardLimits::TYPE_MONTH => 'месячный',			
	);	
	
	public function limitTypeName()	{
		$s = $this->limitTypeNames[$this->limitType];
		if (isset($s))
			return $s;
		else 
			return '<'.$this->limitType.'>'; 
	}
		
	public function enabledVolume($fuelId) {
		if ($fuelId != $this->fuelId)
			return NULL;		
		return $this->orderVolume - $this->usageVolume;
	}
	
	public function registerSale($fuelId, $volume) {
		if ($fuelId != $this->fuelId)
			return;
		$needSave = false;
		if ($this->limitType == CardLimits::TYPE_SUMMARY) {
			$this->orderVolume = $this->orderVolume - $volume; 
			$needSave = true;
		}
		if ($needSave)
				$this->save(); 
	}	
	
	public function cardChanged() {
		$cardModel = new Cards();
		$card = $cardModel->find(array('condition'=>'id=:id','params'=>array(':id'=>$this->cardId)));
		$card->wasChanged();
		$card->save();
	}
	
	protected function afterFind()
	{
		parent::afterFind();
		$this->usageVolume = 0;
		$crit = new CDbCriteria();
		$crit->select = 'sum(-volume) as volume';
		$crit->compare('card.number', $this->card->number);
		$crit->compare('autoCard.number', $this->card->number, false, 'or');
		$crit->compare('operationType', 2);
		$crit->compare('fuelId',  $this->fuelId);
		$crit->with = array('card','autoCard');
		$crit->group = 'card.id';
		if ($this->limitType == CardLimits::TYPE_DAY) {
			$crit->compare('cast(`date` as date)', Yii::app()->DateFormatter->format('yyyy-MM-dd', time()));
			$op = new CardOperations();
			$data = $op->find($crit);
			if (isset($data)) 
				$this->usageVolume = $data->volume;
		}
		else if ($this->limitType == CardLimits::TYPE_MONTH) {
			$dt = new DateTime();
			$day = $dt->format('d') - 1;
			$dt->modify("-{$day} days");
			$crit->compare('cast(`date` as date)', '>='.$dt->format('Y-m-d'));
			$dt->modify("+1 month");
			$dt->modify("-1 days");
			$crit->compare('cast(`date` as date)', '<='.$dt->format('Y-m-d'));			
			$op = new CardOperations();
			$data = $op->find($crit);
			if (isset($data)) 
				$this->usageVolume = $data->volume;
		}		
		if ($this->usageVolume > 0) {
			$this->lastSaleDate = new DateTime();			
		} 
		else {
			$this->usageVolume = 0;
			$this->lastSaleDate = new DateTime();			
		}
	}
	
	public function asString()
	{
		$s = 'карта '.$this->card->number;
		$s = $s.' "'.$this->card->owner.'"';
		$s = $s.', лимит: "'.$this->limitTypeNames[$this->limitType].'"';		
		$s = $s.', продукт: "'.$this->fuel->name.'"';		
		$s = $s.', объем: '.$this->orderVolume;		
		return $s;
	}
	
	protected function beforeSave()
	{		
		$this->cardChanged();
		if ($this->isNewRecord) $this->old = NULL;
		else {
			$c = new CardLimits;
			$this->old = $c->findByPk($this->id);
		}
		return parent::beforeSave();
	}
	
	protected function afterSave()
	{
		if ($this->isNewRecord) {
			Syslog::info('Новый лимит ['.$this->asString().']');
		}
		else {
			if ($this->asString() != $this->old->asString())  Syslog::info('Изменение лимита ['.$this->old->asString().'] -> ['.$this->asString().']');
		}
		return parent::afterSave();	
	}  		
	
	protected function beforeDelete()
	{
		$this->cardChanged();
		return parent::beforeDelete();
	}
	
	protected function afterDelete()
	{
		Syslog::info('Удаление лимита ('.$this->asString().')');
		return parent::afterDelete();
	}		
		
	
}