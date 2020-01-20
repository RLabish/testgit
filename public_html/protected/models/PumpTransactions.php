<?php

/**
 * This is the model class for table "pumptransactions".
 *
 * The followings are the available columns in table 'pumptransactions':
 * @property integer $operId
 * @property integer $state
 * @property integer $terminalId
 * @property integer $pumpId
 * @property string $counterBegin
 * @property string $counterEnd
 *
 * The followings are the available model relations:
 * @property Cardoperations $oper
 * @property Terminals $terminal
 */
class PumpTransactions extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PumpTransactions the static model class
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
		return 'pumptransactions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('operId, state, terminalId', 'required'),
			array('operId, state, terminalId, pumpId', 'numerical', 'integerOnly'=>true),
			array('counterBegin, counterEnd', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('operId, state, terminalId, pumpId, counterBegin, counterEnd', 'safe', 'on'=>'search'),
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
			'oper' => array(self::BELONGS_TO, 'CardOperations', 'operId'),
			'terminal' => array(self::BELONGS_TO, 'Terminals', 'terminalId'),
			'tank' => array(self::BELONGS_TO, 'Tanks', 'tankId'),				
			'pump' => array(self::BELONGS_TO, 'Pumps', 'pumpId'),				
		);
	}
	
	public $contractor = 'ccc';

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'operId' => 'Oper',
			'state' => 'State',
			'terminalId' => 'Terminal',
			'pumpId' => 'Pump',
			'counterBegin' => 'Counter Begin',
			'counterEnd' => 'Counter End',
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

		$criteria->compare('operId',$this->operId);
		$criteria->compare('state',$this->state);
		$criteria->compare('terminalId',$this->terminalId);
		$criteria->compare('pumpId',$this->pumpId);
		$criteria->compare('counterBegin',$this->counterBegin,true);
		$criteria->compare('counterEnd',$this->counterEnd,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}