<?php

/**
 * This is the model class for table "zterm_cardBalance".
 *
 * The followings are the available columns in table 'zterm_cardBalance':
 * @property integer $id
 * @property integer $cardId
 * @property integer $fuelId
 * @property string $volume
 */
class CardBalance extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CardBalance the static model class
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
		return 'cardbalance';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cardId, fuelId', 'required'),
			array('cardId, fuelId', 'numerical', 'integerOnly'=>true),
//			array('volume', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cardId, fuelId, volume', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'card' => array(self::BELONGS_TO, 'Cards', 'cardId'),
			'fuel' => array(self::BELONGS_TO, 'Fuels', 'fuelId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cardId' => 'Card',
			'fuelId' => 'Fuel',
			'volume' => 'Volume',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('cardId',$this->cardId);
		$criteria->compare('fuelId',$this->fuelId);
		$criteria->compare('volume',$this->volume,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}