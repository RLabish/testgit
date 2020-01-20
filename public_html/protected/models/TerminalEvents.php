<?php

/**
 * This is the model class for table "terminalevents".
 *
 * The followings are the available columns in table 'terminalevents':
 * @property integer $id
 * @property string $date
 * @property integer $terminalId
 * @property integer $eventNo
 * @property integer $type
 * @property string $msg
 * @property string $sysInfo
 *
 * The followings are the available model relations:
 * @property Terminals $terminal
 */
class TerminalEvents extends CActiveRecord
{
	const TYPE_SYNC = 0;
	const TYPE_STARTUP = 1;
	const TYPE_SHUTDOWN = 2;
	const TYPE_TANK_SENSOR_STATE = 10;
	
	public $date_range = array();
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TerminalEvents the static model class
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
		return 'terminalevents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, terminalId, eventNo, type, msg', 'required'),
			array('terminalId, eventNo, type', 'numerical', 'integerOnly'=>true),
			array('msg, sysInfo', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, terminalId, eventNo, type, msg, sysInfo, date_range', 'safe', 'on'=>'search'),
			array('id, date, terminalId, msg, date_range', 'safe', 'on'=>'excel'),
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
            'tankState' => array(self::BELONGS_TO, 'TankRealStates', 'tankStateId'),
            'tank' => array(self::BELONGS_TO, 'Tanks', 'tankId'),
            'terminal' => array(self::BELONGS_TO, 'Terminals', 'terminalId'),
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
			'terminalId' => 'Терминал',
			'eventNo' => 'Event No',
			'type' => 'Type',
			'msg' => 'Сообщение',
			'sysInfo' => 'Sys Info',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria=new CDbCriteria;

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
		$criteria->compare('terminalId',$this->terminalId);
		$criteria->compare('eventNo',$this->eventNo);
		$criteria->compare('type',$this->type);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('sysInfo',$this->sysInfo,true);

		if ($this->scenario == 'excel') $criteria->order = 't.date';		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>50,
			),
			'sort' => array(
				'defaultOrder' => 'date DESC',
			),
		));		
	}
}