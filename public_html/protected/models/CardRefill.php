<?php

/**
 * This is the model class for table "cardrefill".
 *
 * The followings are the available columns in table 'cardrefill':
 * @property integer $operId
 * @property integer $userId
 * @property string $document
 *
 * The followings are the available model relations:
 * @property Cardoperations $oper
 * @property Users $user
 */
class CardRefill extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Cardrefill the static model class
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
		return 'cardrefill';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('operId, userId', 'required'),
			array('operId, userId', 'numerical', 'integerOnly'=>true),
			array('document', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('operId, userId, document', 'safe', 'on'=>'search'),
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
			'oper' => array(self::BELONGS_TO, 'Cardoperations', 'operId'),
			'user' => array(self::BELONGS_TO, 'Users', 'userId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'operId' => 'Oper',
			'userId' => 'User',
			'document' => 'Document',
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
		$criteria->compare('userId',$this->userId);
		$criteria->compare('document',$this->document,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}