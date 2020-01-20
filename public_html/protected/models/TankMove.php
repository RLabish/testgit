<?php

/**
 * This is the model class for table "tankmove".
 *
 * The followings are the available columns in table 'tankmove':
 * @property integer $id
 * @property string $date
 * @property integer $tankFromId
 * @property integer $tankToId
 * @property integer $fuelId
 * @property string $doc
 * @property string $volume
 *
 * The followings are the available model relations:
 * @property Fuels $fuel
 * @property Tanks $tankFrom
 * @property Tanks $tankTo
 */

Yii::import('application.extensions.MyUtils');

class TankMove extends CActiveRecord
{
	public $dateFmt;
	public $date_range = array();
		
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TankMove the static model class
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
		return 'tankmove';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('dateFmt, tankFromId, tankToId, volume', 'required'),
			array('dateFmt, tankFromId, volume', 'required'),
			array('tankFromId, tankToId, fuelId', 'numerical', 'integerOnly'=>true),
			array('doc', 'length', 'max'=>255),
			array('volume', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, date_range, tankFromId, tankToId, fuelId, doc, volume', 'safe', 'on'=>'search'),
			array('id, dateFmt, tankFromId, tankToId, doc, volume', 'safe', 'on'=>'modify'),
			array('dateFmt, tankFromId', 'checkAttribute'),
		);
	}
	
	public function checkAttribute($attribute,$params){
		if ($this->hasErrors()) return;
		if($attribute == 'dateFmt') 
//				$this->date = Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->dateFmt);
				$this->date = Yii::app()->DateFormatter->format('yyyy-MM-dd H:m', $this->dateFmt);
				
		if ($attribute == 'tankFromId') 
				$this->fuelId = Tanks::model()->findByPk($this->tankFromId)->fuelId;
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
			'tankFrom' => array(self::BELONGS_TO, 'Tanks', 'tankFromId'),
			'tankTo' => array(self::BELONGS_TO, 'Tanks', 'tankToId'),
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
			'tankFromId' => 'C резервуара',
			'tankToId' => 'В резервуар',
			'fuelId' => 'Вид топлива',
			'depId' => 'Колхоз',
			'doc' => 'Основание',
			'volume' => 'Объем, л',					
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
		
		$criteria->compare('tankFromId',$this->tankFromId);
		$criteria->compare('tankToId',$this->tankToId);
		
		$criteria->compare('t.fuelId',$this->fuelId);
		$criteria->compare('doc',$this->doc,true);
		$criteria->compare('volume',$this->volume,true);

		
		$criteria->with = array('tankFrom', 'tankTo', 'fuel');		
		
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
	}	
	
	public function asString()
	{
		$s = '';		
		$s = $s.' дата: "'.$this->dateFmt.'"';
		$s = $s.', c АЗС: "'.((isset($this->tankFromId)) ? $this->tankFrom->azs->name : '').'"';
		$s = $s.', c р-ра: "'.((isset($this->tankFromId)) ? $this->tankFrom->name : '').'"';
		$s = $s.', на АЗС: "'.((isset($this->tankToId)) ? $this->tankTo->azs->name : '').'"';
		$s = $s.', в р-р: "'.((isset($this->tankToId)) ? $this->tankTo->name : '').'"';
		$s = $s.', основание: "'.$this->doc.'"';
		$s = $s.', продукт: "'.$this->fuel->name.'"';
		$s = $s.', объем: '.$this->volume;
		return $s;
	}	
	private $old;
	protected function beforeSave()
	{
		if ($this->isNewRecord) $this->old = NULL;
		else {
			$c = new TankMove;
			$this->old = $c->findByPk($this->id);
		}		
		return parent::beforeSave();	
	}    
		
	protected function afterSave()
	{
		if ($this->isNewRecord) Syslog::info('Новый документ "Перемещение топлива" ['.$this->asString().']');
		else if ($this->asString() != $this->old->asString())  Syslog::info('Изменение документа "Перемещение топлива" ['.$this->old->asString().'] -> ['.$this->asString().']');
		return parent::afterSave();	
	} 
	
	protected function afterDelete()
	{
		Syslog::info('Удаление документа "Перемещение топлива" ('.$this->asString().')');
		return parent::afterDelete();
	}
}