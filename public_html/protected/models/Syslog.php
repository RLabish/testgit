<?php

/**
 * This is the model class for table "syslog".
 *
 * The followings are the available columns in table 'syslog':
 * @property integer $id
 * @property string $date
 * @property string $user
 * @property string $message
 */
class Syslog extends CActiveRecord
{

	public $date_range = array();
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Syslog the static model class
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
		return 'syslog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user', 'length', 'max'=>20),
			array('message', 'length', 'max'=>255),
			array('message', 'required'),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, user, message, date_range', 'safe', 'on'=>'search'),
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
			'user' => 'Пользователь',
			'message' => 'Действие',
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

		$criteria->compare('date',$this->date,true);
		$criteria->compare('user',$this->user,true);
		$criteria->compare('message',$this->message,true);

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
	
	public static function info($message)
	{
		if (Yii::app()->user->name == 'Guest') return;
		try {
			$x = new Syslog();
			$x->date = MyUtils::datetimeFormat('yyyy-MM-dd HH:mm:ss', time());
			$x->user = Yii::app()->user->name;
			$x->message = $message;
			if (!$x->save())  throw new Exception(print_r($x->errors, true));
		} catch (Exception $e) {
			Yii::log('[Syslog::info] '.$e->getMessage(), 'error');
		}
	}	
}