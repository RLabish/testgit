<?php

/**
 * This is the model class for table "terminals".
 *
 * The followings are the available columns in table 'terminals':
 * @property integer $id
 * @property string $login
 * @property string $psw
 * @property string $name
 * @property integer $active
 *
 * The followings are the available model relations:
 * @property Pumps[] $pumps
 * @property Pumptransactions[] $pumptransactions
 * @property Tanks[] $tanks
 */
class Terminals extends CActiveRecord
{
	public $azsName;	
    public $isOk;
    public $stateInfo;	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Terminals the static model class
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
		return 'terminals';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('azsId, name, sn, active, transNo, eventNo', 'required'),
			array('transNo, eventNo', 'numerical', 'integerOnly'=>true),
			array('note', 'safe'),
			array('id, name, active', 'safe', 'on'=>'search'),
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
			'azs' => array(self::BELONGS_TO, 'Azs', 'azsId'),
			'pumps' => array(self::HAS_MANY, 'Pumps', 'terminalId'),
			'pumptransactions' => array(self::HAS_MANY, 'Pumptransactions', 'terminalId'),
			'tanks' => array(self::HAS_MANY, 'Tanks', 'terminalId'),
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
			'azsName' => 'АЗС',
			'azsId' => 'АЗС',
			'sn' => 'Серийный номер',
			'note' => 'Примечание',
			'active' => 'Активен',
			'transNo' => 'Считано транзакций',
			'eventNo' => 'Считано событий',
		);
	}

    protected function afterFind()
    {
        parent::afterFind();
		$this->azsName = $this->azs->name;
        $this->isOk = true;
        $this->stateInfo = array();

        $dtnow = new DateTime('NOW');
        if ($this->syncDate) {
            $diff = $dtnow->diff(DateTime::createFromFormat('Y-m-d H:i:s', $this->syncDate));
            $diff_min = ($diff->y * 365 + $diff->m * 31 + $diff->d) * 24 + $diff->h * 60 + $diff->i;
            if ($diff_min > 60 * 1) {
                $this->isOk = false;
                $this->stateInfo[] = 'Нет связи';
            }
        }
        if (property_exists($this, 'printerStatus') && ($this->printerStatus != 0)) {
            $this->isOk = false;
            if (($this->printerStatus) && 1 !=  0) $err = 'нет бумаги';
            else if (($this->printerStatus) && 4 !=  0) $err = 'мало бумаги';
            else $err = $this->printerStatus;
            $this->stateInfo[] = 'Ошибка принтера :'.$err;
        }

        if (count($this->stateInfo) == 0) {
            if ($this->isOk) $this->stateInfo[] = 'OK';
            else $this->stateInfo[] = 'ERROR';
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
		$criteria->compare('login',$this->login,true);
		$criteria->compare('psw',$this->psw,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('active',$this->active);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}