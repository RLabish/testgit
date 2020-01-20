<?php

/**
 * This is the model class for table "organizationoperations".
 *
 * The followings are the available columns in table 'organizationoperations':
 * @property integer $id
 * @property integer $type
 * @property string $date
 * @property integer $orgId
 * @property integer $fuelId
 * @property string $volume
 * @property string $balance
 * @property string $note
 *
 * The followings are the available model relations:
 * @property Fuels $fuel
 * @property Organizations $org
 */
class OrganizationOperations extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrganizationOperations the static model class
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
		return 'organizationoperations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, date, fuelId, volume, balance', 'required'),
			array('type, orgId, fuelId', 'numerical', 'integerOnly'=>true),
			array('volume, balance', 'length', 'max'=>8),
			array('note', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, date, orgId, fuelId, volume, balance, note', 'safe', 'on'=>'search'),
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
			'fuel' => array(self::BELONGS_TO, 'Fuels', 'fuelId'),
			'org' => array(self::BELONGS_TO, 'Organizations', 'orgId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'date' => 'Date',
			'orgId' => 'Org',
			'fuelId' => 'Вид топлива',
			'volume' => 'Объем,л',
			'balance' => 'Balance',
			'note' => 'Основание',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('orgId',$this->orgId);
		$criteria->compare('fuelId',$this->fuelId);
		$criteria->compare('volume',$this->volume,true);
		$criteria->compare('balance',$this->balance,true);
		$criteria->compare('note',$this->note,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}