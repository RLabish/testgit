<?php

/**
 * This is the model class for table "organizationbalance".
 *
 * The followings are the available columns in table 'organizationbalance':
 * @property integer $id
 * @property integer $orgId
 * @property integer $fuelId
 * @property string $volume
 */
class OrganizationBalance extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrganizationBalance the static model class
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
		return 'organizationbalance';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('orgId, fuelId, volume', 'required'),
			array('orgId, fuelId', 'numerical', 'integerOnly'=>true),
			array('volume', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, orgId, fuelId, volume', 'safe', 'on'=>'search'),
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
            'organization' => array(self::BELONGS_TO, 'Organizations', 'orgId'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'orgId' => 'Org',
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
		$criteria->compare('orgId',$this->orgId);
		$criteria->compare('fuelId',$this->fuelId);
		$criteria->compare('volume',$this->volume,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}