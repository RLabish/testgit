<?php

/**
 * This is the model class for table "zterm_cards".
 *
 * The followings are the available columns in table 'zterm_cards':
 * @property integer $id
 * @property string $number
 * @property integer $state
 * @property string $expire
 * @property string $owner
 *
 * The followings are the available model relations:
 * @property Cardlimits[] $cardlimits
 * @property Transactions[] $transactions
 */
Yii::import('application.extensions.MyUtils');
 
class Cards extends CActiveRecord {
	
	const TYPE_DEBIT = 0;
	const TYPE_LIMITED = 3;
	const TYPE_SERVICE = 4;
	const TYPE_MOVE = 5;
//	const TYPE_AUTO_CALIBR = 6;

	const OWNER_TYPE_CUSTOM = 0;
	const OWNER_TYPE_DRIVER = 1;
	const OWNER_TYPE_AUTO = 2;
	
	public $cardStates 	= array(
				0 => 'активна',	
    			1 => 'заблокирована',
    		);
	
	public $typeNames = array(
			Cards::TYPE_LIMITED => 'лимитная',
			Cards::TYPE_DEBIT => 'расчетная',
			Cards::TYPE_SERVICE => 'техпролив',
			Cards::TYPE_MOVE => 'перемещение',
//			Cards::TYPE_AUTO_CALIBR => 'калибровка бака автомобиля',
	);

	public $ownerTypeNames = array(
			Cards::OWNER_TYPE_CUSTOM => 'по умолчанию',
			Cards::OWNER_TYPE_DRIVER => 'водитель',
			Cards::OWNER_TYPE_AUTO => 'автомобиль',
	);

	private $old;
	public $expireFmt;
    public $ok;
    public $stateAsText;
	public $organizationName;	
	public $typeExt;
	public $typeExtNames;

	private static $_typeExtNames = array();


	public function __construct($scenario='insert') {
		parent::__construct($scenario);
		if (count(self::$_typeExtNames) == 0) {
			self::$_typeExtNames = array(
				Cards::TYPE_LIMITED => 'лимитная',
				Cards::TYPE_SERVICE => 'техпролив',
				Cards::TYPE_MOVE => 'перемещение',
			);
			$criteria=new CDbCriteria;
			$criteria->with = array('azs', 'fuel');
			$criteria->order = 'azs.name,t.name';
			$m = new Tanks();
			foreach ($m->findall($criteria) as $x) {
				self::$_typeExtNames[1000000 + $x->id] = 'перемещение в р-р '.$x->azs->name.'-'.$x->name.' ('.$x->fuel->name.')';
			}
		}
		$this->typeExtNames = self::$_typeExtNames;
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Cards the static model class
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
		return 'cards';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	
	public function rules()
	{
		return array_merge(
			array(
				array('number, state,owner,expireFmt', 'required'),
				array('number', 'application.extensions.CardNoValidator'),
				array('state', 'numerical', 'integerOnly'=>true),
				array('number', 'length', 'max'=>20),
				array('number', 'unique', 'className'=>'Cards'),
				array('owner', 'length', 'max'=>60),
				array('ownerType, pin, type, typeExt, description, gpsId', 'safe'),
				array('expireFmt', 'checkAttribute'),
				array('number, state, expireFmt, owner, description, expire, organizationName, type', 'safe', 'on'=>'search'),
			),

			(Yii::app()->theme->name == 'clients') ? array(
				/*array('organizationName', 'required'),*/
				array('organizationName', 'checkAttribute'), 
			): array()

		);
	}
	
	public function checkAttribute($attribute,$params){

		if(!$this->hasErrors() && ($attribute == 'expireFmt')) {
			$now = Yii::app()->DateFormatter->format('yyyy-MM-dd', time());
			$this->expire = Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->expireFmt);
			
			if ($this->expire < $now) {
//				$message=Yii::t('yii','Дата "Действует до" меньше текущей.');
//				$this->addError($attribute,$message);
			}
			return;
    	}

		if ((Yii::app()->theme->name == 'clients') && ($attribute == 'organizationName')) {
			if (!isset($this->organizationName) or ($this->organizationName == '')) {
				$this->orgId = null;
				return;
			}
			$org=Organizations::model()->find("name LIKE :name", array(':name'=>$this->organizationName));
			if (isset($org)) 
				$this->orgId = $org->id;
			else {
				if (isset($this->organizationName) and ($this->organizationName != ''))
					$this->addError('organizationName', 'Организация не зарегистрирована');
			}
			return;
		}
	}	

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return  array_merge(
			array(
				'cardbalances' => array(self::HAS_MANY, 'CardBalance', 'cardId'),
				'cardoperations' => array(self::HAS_MANY, 'CardOperations', 'cardId'),
				'limits' => array(self::HAS_MANY, 'CardLimits', 'cardId'),
				'operationsCount'=>array(self::STAT, 'CardOperations', 'cardId'),
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
			'id' => 'id',
			'number' => 'Номер',
			'type' => 'Тип',
			'typeExt' => 'Тип',
			'ownerType' => 'Идентификация',
			'owner' => 'Владелец',
			'description' => 'Описание',
			'state' => 'Состояние',
			'stateAsText' => 'Состояние',
			'expire' => 'Действует до',
			'expireFmt' => 'Действует до',
			'organizationName' => (Yii::app()->theme->name == 'clients')?'Клиент':'Организация',			
			'gpsId' => 'GPS',
		);
	}
    
	protected function afterFind()
	{
		parent::afterFind();
		$this->expireFmt = MyUtils::datetimeFormat('dd.MM.yyyy', $this->expire);		
		$this->ok = false;
		switch ($this->state) {
			case 0:
				$now = Yii::app()->DateFormatter->format('yyyy-MM-dd', time());
				if ($this->expire < $now)
					$this->stateAsText = 'просрочена';
				else {
					$this->ok = true;
					$this->stateAsText = 'активна';
				}
				break;
			case 1:
				$this->stateAsText = 'заблокирована';
				break;
			default:
				$this->stateAsText = 'неизвестное состояние '.$this->model->state;								
		}
		if (isset($this->organization)) $this->organizationName = $this->organization->name;	
		$this->typeExt = (($this->type == Cards::TYPE_MOVE) && isset($this->tankToId)) ? $this->typeExt = 1000000 + $this->tankToId: $this->typeExt = $this->type;
	}	
	
	public function wasChanged() {
		$this->update_date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());		
	}
	
	public function asString()
	{
		$s = $this->number;		
		$s = $s.', "'.$this->owner.'"';
		$s = $s.', "'.$this->description.'"';;
		$s = $s.', тип: "'.$this ->typeNames[$this->type].'"';	
		$s = $s.', состояние: "'.$this->cardStates[$this->state].'"';	
		$s = $s.', до '.Yii::app()->DateFormatter->format('dd.MM.yyyy', $this->expire).'';	
		return $s;
	}

	protected function beforeSave()
	{
		$this->wasChanged();		
		if ($this->isNewRecord) $this->old = NULL;
		else {
			$c = new Cards;
			$this->old = $c->findByPk($this->id);
		}		
		if (!$this->isNewRecord && ($this->old->typeExt != $this->typeExt) || $this->isNewRecord && ($this->typeExt !== NULL) ) {
			if ($this->typeExt > 1000000) {
				$this->type = Cards::TYPE_MOVE;
				$this->tankToId = $this->typeExt - 1000000;
			}
			else 
				$this->type = $this->typeExt;						
		}
		return parent::beforeSave();	
	}    
	
	protected function afterSave()
	{
		if ($this->isNewRecord) {
			Syslog::info('Новая карта ['.$this->asString().']');
		}
		else {
			if ($this->asString() != $this->old->asString())  Syslog::info('Изменение карты ['.$this->old->asString().'] -> ['.$this->asString().']');
		}
		
		return parent::afterSave();	
	} 	
	
	protected function beforeDelete()
	{
		if ($this->operationsCount > 0)
			throw new CHttpException(403, 'Невозможно удалить карту: есть операции по карте.');
		return parent::beforeDelete();
	}	
	
	protected function afterDelete()
	{
		Syslog::info('Удаление карты ('.$this->asString().')');
		return parent::afterDelete();
	}		
	
	
	public function gpsNames() {
		$r = Yii::app()->wialonApi-> getObjects();
		$res = array(0 => '',);
		foreach ($r as $x) {
			$res[$x['id']] = $x['nm'];
			
		}
		return $res;
	}
	
	public function balanceByFuelId($fuelId) {
		$limit = NULL;
		if ($this->type == Cards::TYPE_LIMITED) {
			foreach($this->limits as $lim) {
				$v = $lim->enabledVolume($fuelId);
				if ($v === NULL)
					continue;
				if ($limit === NULL)
					$limit = $v;
				else if ($v < $limit) 
					$limit = $v;					
			}
		}
		else if ($this->type == Cards::TYPE_DEBIT) {
			$balances = $this->cardbalances;
	  		foreach($balances as $b) {
	  			if ($b->fuelId == $fuelId) {
					$limit = $b->volume;
					break;
				}
	  		}
		}
		else if ($this->type == Cards::TYPE_SERVICE) {
			$limit = 999999.99;
		}
		else if ($this->type == Cards::TYPE_MOVE) {
			$limit = 999999.99;
		}
		
		if  (Yii::app()->theme->name == 'clients') {
			if (isset($this->orgId)) {
				$v = $this->organization->balanceByFuelId($fuelId);
				if ($limit === NULL)
					$limit = $v;
				else if ($v < $limit)
					$limit = $v;
			}
		}
		
		if ($limit === NULL)
				$limit = 0;		
	  	return $limit;
	}
	
	public function incbalance($fuelId, $volume, $note) {
		$connection=Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$sql = 'call cardRefill(:in_userId, :in_cardId, :in_fuelId, :in_volume, :in_document)';
			$command = $connection->createCommand($sql);
			$command->bindValues(array (
				'in_userId'=>Yii::app()->user->id,
				'in_cardId'=>$this->id,
				'in_fuelId'=>$fuelId,
				'in_volume'=>$volume,
				'in_document'=>$note,
			));
			$command->execute();
			$transaction->commit();
		} catch(Exception $e) {
			$transaction->rollBack();
			throw $e;
		}						
		$this->wasChanged();		
		$this->save();
	}		

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->with = array('cardbalances');
		if (Yii::app()->theme->name == 'clients') {
			$criteria->with = array_merge($criteria->with, array('organization'));
		}

		$criteria->compare('t.id','>0');
		$criteria->compare('t.id',$this->id);
		$criteria->compare('number',$this->number,true);
		if ($this->expire == 1) {
			$criteria->compare('state',0);
			$criteria->compare('expire','>='.Yii::app()->DateFormatter->format('yyyy-MM-dd', time()));
		}
		$criteria->compare('owner',$this->owner,true);	
		$this->description = trim($this->description);
		$criteria->compare('description',$this->description,true);
		if (isset($this->organizationName)) {
			$this->organizationName = trim($this->organizationName);
			$criteria->compare('organization.name',$this->organizationName,true);
		}
		
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'sort' => array(
      			'defaultOrder' => 'owner ASC',
		        'attributes'=>array(
					'*',
					'organizationName'=>array(
						'asc'=>'organization.name,owner',
						'desc'=>'organization.name,owner DESC',
					),
				),
			),
		));
	}		
}