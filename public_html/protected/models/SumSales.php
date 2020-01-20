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
class SumSales extends CActiveRecord
{
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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('number,state,owner', 'required'),
			array('state', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>20),
			array('number', 'unique', 'className'=>'Cards'),			
			array('owner', 'length', 'max'=>60),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('number, state, owner, description', 'safe', 'on'=>'search'),

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
			'cardbalances' => array(self::HAS_MANY, 'CardBalance', 'cardId'),
			'cardoperations' => array(self::HAS_MANY, 'CardOperations', 'cardId'),
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
			'owner' => 'Владелец',		
			'description' => 'Описание',
			'state' => 'Состояние',
			'expire' => 'Действует до',
		);
	}
	public $dateFrom;
	public $dateTo;
    
	function findSumSales()
	{
		$GLOBALS['SumSales.sales'] = CardOperations::model()->findAll(array(
				'select'=>'cardId,fuelId,sum(-volume) as volume',
				'group'=>'cardId,fuelId',
				'condition'=>'(operationType=2 or operationType=3) and (cast(`date` as date)>=:dateFrom)and (cast(`date` as date)<=:dateTo)',
				'params'=>array(':dateFrom'=>$this->dateFrom, ':dateTo'=>$this->dateTo),
		));
	} 
	
	public function sumVolumeByCard($fuelId, $cardId) {
		foreach($GLOBALS['SumSales.sales'] as $b) {
			if (($b->fuelId == $fuelId) && ($b->cardId == $cardId))
				if ($b->volume == 0)
					break;
				else
					return $b->volume;
		}
		return "";
	}
	
	public function sumVolume($fuelId) {
		$res = 0;
		foreach($GLOBALS['SumSales.sales'] as $b) {
			if ($b->fuelId == $fuelId)
				return $res = $res + $b->volume;
		}
		return $res;
	}
		
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$this->findSumSales();
		
		$criteria=new CDbCriteria;
		$criteria->condition = 'exists (select 1 from {{cardoperations}} a where (a.cardId = `t`.`id`) and (operationType=2 or operationType=3) and (cast(`date` as date)>=:dateFrom)and (cast(`date` as date)<=:dateTo))';
		$criteria->params = array(':dateFrom'=>$this->dateFrom, ':dateTo'=>$this->dateTo);		
		$criteria->compare('id',$this->id);
		$criteria->compare('number',$this->number,true);
		$criteria->compare('state',$this->state);
		$criteria->compare('expire',$this->expire,true);
		$criteria->compare('owner',$this->owner,true);
		
		
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize'=>50,
			),				
			'sort' => array(
      			'defaultOrder' => 'owner ASC',
			),
		));				
	}
}