<?php

/**
 * This is the model class for table "tankinventory".
 *
 * The followings are the available columns in table 'tankinventory':
 * @property integer $id
 * @property string $date
 * @property integer $tankId
 * @property integer $fuelId
 * @property string $doc
 * @property string $volume
 *
 * The followings are the available model relations:
 * @property Fuels $fuel
 * @property Tanks $tank
 */

Yii::import('application.extensions.MyUtils');

class TankInventory extends CActiveRecord
{
	public $dateFmt;
	public $date_range = array();
	public $volumeDiff;
	public $azsId;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TankInventory the static model class
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
		return 'tankinventory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dateFmt, tankId, operVolume, operRest', 'required'),
			array('tankId, fuelId', 'numerical', 'integerOnly'=>true),
			array('operVolume, operRest', 'numerical', 'integerOnly'=>false),
			array('doc', 'length', 'max'=>255),
			array('volume', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, dateFmt, date_range, depId, azsId, tankId, fuelId, doc, oldVolume, operVolume, operRest', 'safe', 'on'=>'search'),
			array('id, dateFmt, tankId, doc, volume', 'safe', 'on'=>'modify'),
			array('dateFmt, tankId', 'checkAttribute'),
		);
	}
	
	public function checkAttribute($attribute,$params){
		if ($this->hasErrors()) return;
		if($attribute == 'dateFmt') 
//				$this->date = Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->dateFmt);
				$this->date = Yii::app()->DateFormatter->format('yyyy-MM-dd H:m', $this->dateFmt);
				
		else if ($attribute == 'tankId') 
				$this->fuelId = Tanks::model()->findByPk($this->tankId)->fuelId;
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'date' => 'Дата',
			'dateFmt' => 'Дата',
			'tankId' => 'Резервуар',
			'fuelId' => 'Вид топлива',
			'doc' => 'Основание',
			'volume' => 'Объем,л',
			'operVolume' => 'Объем топлива,л',
			'operRest' => 'Мертвый остаток,л',				
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
		$criteria->condition = '1=1';
		if (isset($this->date_range['from']) && ($this->date_range['from'] != ''))
			$criteria->compare('date',">= ".MyUtils::datetimeFormat('yyyy-MM-dd', $this->date_range['from']));
		if (isset($this->date_range['to']) && ($this->date_range['to'] != ''))
			$criteria->compare('date',"<= ".MyUtils::datetimeFormat('yyyy-MM-dd', $this->date_range['to']));	
		$criteria->compare('azs.id',$this->azsId);
		$criteria->compare('tankId',$this->tankId);
		$criteria->compare('t.fuelId',$this->fuelId);
		$criteria->compare('doc',$this->doc,true);
		$criteria->compare('volume',$this->volume,true);

		$criteria->with = array('tank', 'fuel', 'tank.terminal', 'tank.azs');		
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => array(
					'defaultOrder' => 'date asc',
			),				
		));
	}
	
	protected function afterFind()
	{
		parent::afterFind();
///		$this->dateFmt = Yii::app()->DateFormatter->formatDateTime($this->date,'medium',null);
		$this->dateFmt = MyUtils::datetimeFormat('dd.MM.yyyy HH:mm', $this->date);	
		$this->volumeDiff = $this->oldVolume - ($this->operVolume + $this->operRest);		
	}
	
	public function asString()
	{
		$s = '';		
		$s = $s.' дата: "'.$this->dateFmt.'"';
		$s = $s.', основание: "'.$this->doc.'"';
		$s = $s.', АЗС: "'.$this->tank->azs->name.'"';
		$s = $s.', р-р: "'.$this->tank->number.'"';
		$s = $s.', продукт: "'.$this->fuel->name.'"';
		$s = $s.', объем: '.$this->operVolume;
		return $s;
	}
	
	private $old;
	
	protected function beforeSave()
	{
		if ($this->isNewRecord) $this->old = NULL;
		else {
			$c = new TankInventory;
			$this->old = $c->findByPk($this->id);
		}				
		
		if (!isset($this->oldVolume)) {
			$crit=new CDbCriteria;
			$crit->compare('t.tankId', $this->tankId);
			$crit->compare('date',"<= $this->date");			
			$crit->order = '`date` desc';			
			$model = new TankBookStates();
			$res = $model->find($crit);
			if (isset($res)) 
				$this->oldVolume = $res->fuelVolume;
			else
				$this->oldVolume = 0;			
		}
		$this->volume = $this->operVolume + $this->operRest;		
		return parent::beforeSave();
	}	
	
	protected function afterSave()
	{
		if ($this->isNewRecord) Syslog::info('Новый документ "Инвентаризация" ['.$this->asString().']');
		else if ($this->asString() != $this->old->asString())  Syslog::info('Изменение документа "Инвентаризация" ['.$this->old->asString().'] -> ['.$this->asString().']');
		return parent::afterSave();	
	} 
	
	protected function afterDelete()
	{
		Syslog::info('Удаление документа "Инвентаризация" ('.$this->asString().')');
		return parent::afterDelete();
	}		
}