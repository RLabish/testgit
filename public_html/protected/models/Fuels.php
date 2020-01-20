<?php

/**
 * This is the model class for table "zterm_fuels".
 *
 * The followings are the available columns in table 'zterm_fuels':
 * @property integer $id
 * @property integer $code
 * @property string $name
 */
class Fuels extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Fuels the static model class
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
		return 'fuels';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, name', 'required'),
			array('code', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>20),
			array('note', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, code, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        return array(
            'cardbalances' => array(self::HAS_MANY, 'Cardbalance', 'fuelId'),
            'cardoperations' => array(self::HAS_MANY, 'Cardoperations', 'fuelId'),
            'incomes' => array(self::HAS_MANY, 'Incomes', 'fuelId'),
            'tankincomes' => array(self::HAS_MANY, 'Tankincome', 'fuelId'),
            'tankinventories' => array(self::HAS_MANY, 'Tankinventory', 'fuelId'),
            'tankmoves' => array(self::HAS_MANY, 'Tankmove', 'fuelId'),
            'tankRealStates' => array(self::HAS_MANY, 'TankRealStates', 'fuelId'),
            'tankBookStates' => array(self::HAS_MANY, 'TankBookStates', 'fuelId'),
        	'tanks' => array(self::HAS_MANY, 'Tanks', 'fuelId'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => 'Код',
			'name' => 'Наименование',
			'note' => 'Примечание',			
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
		$criteria->compare('code',$this->code);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'sort' => array(
      			'defaultOrder' => 'name ASC',
   			),
		));	
	}	
	
	public static function columns($colDef = array())
	{
		$fuels	= Fuels::model()->findAll(array('select'=>'id,name', 'order'=>'code',));
		$i = 1;
		$columns  = array();
		foreach($fuels as $f) {
			$col = $colDef;
			foreach ($col as $key => $value) {
				if ((gettype($value)== 'string') && (strpos($value, '{') !== false)) {
					$s = $value;
					$s = str_replace('{FuelName}', $f->name, $s);
					$s = str_replace('{FuelId}', $f->id, $s);
					$col[$key] = $s;
				}
				if ($key == 'footerValue') {
					$col['footer'] = evaluateExpression($value);
					unset ($col['footerValue']);
					
				}				
			}
				
			if (!isset($col['header']))
				$col['header'] = $f->name;
			if (!isset($col['value']))
				$col['value'] = $f->id;
			if (!isset($col['htmlOptions']))
				$col['htmlOptions'] = array('style'=>"text-align:right", );
			$columns[$i++] = $col;
		}
		return $columns;
	}
	
}