<?php

/**
 * This is the model class for table "pumps".
 *
 * The followings are the available columns in table 'pumps':
 * @property integer $id
 * @property integer $terminalId
 * @property integer $pumpNo
 * @property integer $nozzleNo
 * @property string $counter
 * @property integer $tankId
 *
 * The followings are the available model relations:
 * @property Pumpcounters[] $pumpcounters
 * @property Tanks $tank
 * @property Terminals $terminal
 */
class Pumps extends CActiveRecord
{
	public $azsId;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Pumps the static model class
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
		return 'pumps';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('terminalId, pumpNo, nozzleNo, azsId, terminalId, tankId', 'required'),
			array('terminalId, pumpNo, nozzleNo, tankId', 'numerical', 'integerOnly'=>true),
			array('counter', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, terminalId, pumpNo, nozzleNo, counter, tankId', 'safe', 'on'=>'search'),
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
			'pumpcounters' => array(self::HAS_MANY, 'Pumpcounters', 'pumpId'),
			'terminal' => array(self::BELONGS_TO, 'Terminals', 'terminalId'),
			'tank' => array(self::BELONGS_TO, 'Tanks', 'tankId'),
			'pump' => array(self::BELONGS_TO, 'Pumps', 'pumpId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'terminalId' => 'Терминал',
			'pumpNo' => 'ТРК',
			'nozzleNo' => 'Пистолет',
			'counter' => 'Counter',
			'tankId' => 'Резервуар',
			'azsId' => 'АЗС',
			'fuelId' => 'Топливо',
		);
	}

	public function afterFind()
	{
		parent::afterFind();
	//	$this->azsName = $this->azs->name;
//		if (isset($this->terminal)) $this->terminalName = $this->terminal->name;
		if (isset($this->tank)) {
			$this->azsId = $this->tank->azsId;			
		}		
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
		$criteria->compare('terminalId',$this->terminalId);
		$criteria->compare('pumpNo',$this->pumpNo);
		$criteria->compare('nozzleNo',$this->nozzleNo);
		$criteria->compare('counter',$this->counter,true);
		$criteria->compare('tankId',$this->tankId);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}