<?php

/**
 * This is the model class for table "organizations".
 *
 * The followings are the available columns in table 'organizations':
 * @property integer $id
 * @property string $name
 * @property string $description
 */
class Organizations extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Organizations the static model class
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
		return 'organizations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name, description', 'length', 'max'=>255),
			array('controlBalance', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, description, controlBalance', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
       return array(
            'cardoperations' => array(self::HAS_MANY, 'Cardoperations', 'orgId'),
            'cards' => array(self::HAS_MANY, 'Cards', 'orgId'),
            'organizationbalances' => array(self::HAS_MANY, 'OrganizationBalance', 'orgId'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Наименование',
			'description' => 'Описание',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => array(
      			'defaultOrder' => 'name ASC',
			),			
		));
	}
	
	public function balanceByFuelId($fuelId) {
		$balances = $this->organizationbalances;
  		foreach($balances as $b) {
  			if ($b->fuelId == $fuelId)
	  			return $b->volume;
	  	}		
	  	return 0;
	}	
	
	public function incBalance($fuelId, $volume, $note) {
		$date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());
		$balance = null;
  		foreach($this->organizationbalances as $b) {
  			if ($b->fuelId == $fuelId) {
	  			$balance = $b;
				break;
			}
	  	}	
		if ($balance == null) {
			$balance = new OrganizationBalance();
			$balance->orgId = $this->id;
			$balance->fuelId = $fuelId;
			$balance->volume = 0;		
			if (!$balance->save())
				throw new Exception($balance->getErrors()); 		
		}

		$cardoper = new CardOperations();
		$cardoper->date = $date;
		$cardoper->operationType = CardOperations::TYPE_CLIENT_REFILL; 		
		$cardoper->cardId = 0;
		$cardoper->orgId = $this->id;
		$cardoper->fuelId = $fuelId;
		$cardoper->volume = $volume;
		$cardoper->balance = $balance->volume + $volume;
		$cardoper->state = 0;
		if (!$cardoper->save()) 
			throw new Exception(print_r($oper->getErrors(), true));
		$cardoper->id = Yii::app()->db->lastInsertID;
		
		$oper = new OrganizationOperations();
		$oper->orgId = $this->id;
		$oper->type = 1;
		$oper->date = $date; 			
		$oper->fuelId = $fuelId;
		$oper->volume = $volume;
		$oper->balance = $balance->volume + $volume;
		$oper->note = $note;
		if (!$oper->save())
			throw new Exception(print_r($oper->getErrors(), true)); 
			
		$balance->volume = $balance->volume + $volume;
		if (!$balance->save())
			throw new Exception($balance->getErrors()); 		
	}	
	
}