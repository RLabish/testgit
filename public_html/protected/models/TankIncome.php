<?php

/**
 * This is the model class for table "tankincome".
 *
 * The followings are the available columns in table 'tankincome':
 * @property integer $id
 * @property string $date
 * @property integer $tankId
 * @property integer $fuelId
 * @property string $doc
 * @property integer $supplierId
 * @property string $volume
 *
 * The followings are the available model relations:
 * @property Suppliers $supplier
 * @property Fuels $fuel
 * @property Tanks $tank
 */

Yii::import('application.extensions.MyUtils');

class TankIncome extends CActiveRecord
{
	public $dateFmt;
	public $supplierName;
	public $date_range = array();
	public $azsId;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TankIncome the static model class
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
		return 'tankincome';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dateFmt, tankId, supplierName, volume', 'required'),
			array('tankId, fuelId, supplierId', 'numerical', 'integerOnly'=>true),
			array('doc', 'length', 'max'=>255),
			array('volume', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, dateFmt, date_range, tankId, azsId, fuelId, doc, supplierId, volume', 'safe', 'on'=>'search'),				
     		array('id, dateFmt, tankId, azsId, depId, doc, supplierName, volume', 'safe', 'on'=>'modify'),
			array('dateFmt, supplierName, tankId', 'checkAttribute'),
		);
	}
	
	public function checkAttribute($attribute,$params){
		if($attribute == 'dateFmt') {
			if (!$this->hasErrors())
//				$this->date = Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->dateFmt);
				$this->date = Yii::app()->DateFormatter->format('yyyy-MM-dd H:m', $this->dateFmt);

		}
		else if ($attribute == 'supplierName') {
			$supplier=Suppliers::model()->find("name LIKE :name", array(':name'=>$this->supplierName.'%'));
			if (isset($supplier))
				$this->supplierId = $supplier->id;
			else
				$this->addError('supplierName', 'Поставщик не зарегистрирован');
		}
		if ($attribute == 'tankId') {
			if (!$this->hasErrors() && !isset($this->fuelId)) {
				$this->fuelId = Tanks::model()->findByPk($this->tankId)->fuelId;			
			}			
		}
	}	

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'supplier' => array(self::BELONGS_TO, 'Suppliers', 'supplierId'),
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
			'azsId' => 'АЗС',
			'tankId' => 'Резервуар',
			'fuelId' => 'Вид топлива',
			'doc' => 'ТТН',
			'supplierId' => 'Поставщик',
			'supplierName' => 'Поставщик',
			'volume' => 'Объем,л',
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
		$criteria->compare('t.tankId',$this->tankId);
		$criteria->compare('t.fuelId',$this->fuelId);
		$criteria->compare('doc',$this->doc,true);
		$criteria->compare('supplierId',$this->supplierId);
		if (isset($this->supplierName)) {
			$this->supplierName = trim($this->supplierName);
			$criteria->condition = $criteria->condition.' and supplier.name like "'.$this->supplierName.'%"';
		}		

		$criteria->with = array('supplier', 'tank', 'fuel', 'tank.terminal', 'tank.azs');

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
		if ($this->tableSchema->columns['date']->dbType == 'date')
			$this->dateFmt = Yii::app()->DateFormatter->formatDateTime($this->date,'medium',null);
		else
			$this->dateFmt = MyUtils::datetimeFormat('dd.MM.yyyy HH:mm', $this->date);				
		$this->supplierName = ($this->supplier) ? $this->supplier->name : '';
	}	
	
	public function asString()
	{
		$s = '';		
		$s = $s.' дата: "'.$this->dateFmt.'"';
		$s = $s.', ТТН: "'.$this->doc.'"';
		$s = $s.', поставщик: "'.$this->supplierName.'"';
		$s = $s.', АЗС: "'.$this->tank->azs->name.'"';
		$s = $s.', р-р: "'.$this->tank->number.'"';
		$s = $s.', продукт: "'.$this->fuel->name.'"';
		$s = $s.', объем: '.$this->volume;
		return $s;
	}

	private $old;
	protected function beforeSave()
	{
		if ($this->isNewRecord) $this->old = NULL;
		else {
			$c = new TankIncome;
			$this->old = $c->findByPk($this->id);
		}		
		return parent::beforeSave();	
	}    
		
	protected function afterSave()
	{
		if ($this->isNewRecord) Syslog::info('Новый документ "Прием топлива" ['.$this->asString().']');
		else if ($this->asString() != $this->old->asString())  Syslog::info('Изменение документа "Прием топлива" ['.$this->old->asString().'] -> ['.$this->asString().']');
		return parent::afterSave();	
	} 
	
	protected function afterDelete()
	{
		Syslog::info('Удаление документа "Прием топлива" ('.$this->asString().')');
		return parent::afterDelete();
	}
	
	
}