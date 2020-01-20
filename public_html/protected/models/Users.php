<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $fullname
 */
class Users extends CActiveRecord
{
	public $role;
	public $rightDefs = array();
	public $rights = array();
	public $curpasswd;
	public $newpasswd;
	public $newpasswd2;
	public $passwd;
	public $passwd2;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function currentUser()
	{
		$m = new Users();
		return $m->find("id=:id", array(":id"=>Yii::app()->user->Id));
	}
	

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password', 'length', 'max'=>20),
			array('fullname', 'length', 'max'=>50),
			array('username', 'unique',),
			array('fullname', 'unique',),
			array('id, username, fullname, description', 'safe'),

			array('fullname, username, role', 'required', 'on'=>'update'),				
			array('fullname, username, role, passwd, passwd2', 'required', 'on'=>'create'),				
			array('passwd, passwd2', 'required', 'on'=>'newpassword'),
//			array('username, password, curpasswd, newpasswd, newpasswd2', 'required', 'on'=>'editself'),

			array('curpasswd, newpasswd, newpasswd2', 'required', 'on'=>'editself'),
			array('fullname, username, description', 'unsafe', 'on'=>'editself'),
			
			array('curpasswd, newpasswd, newpasswd2', 'validateEditSelf', 'on'=>'editself'),
			array('passwd, passwd2', 'validateCheckPassword', 'on'=>array('create', 'newpassword')),
//			array('passwd, passwd2', 'validateCheckPassword', 'on'=>'newpassword'),
			array('rights', 'safe', 'on'=>'create'),
			array('rights', 'safe', 'on'=>'update'),
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
			'sessions' => array(self::HAS_MANY, 'Sessions', 'user_id'),				
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Логин',
			'fullname' => 'Имя',
			'role' => 'Роль',
			'description' => 'Описание',
			'curpasswd' => 'Пароль',
			'newpasswd' => 'Новый пароль',
			'newpasswd2' => 'Подтверждение',
			'passwd' => 'Пароль',
			'passwd2' => 'Подтверждение',
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

		$criteria->compare('id','>0');
		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('fullname',$this->fullname,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => array(
      			'defaultOrder' => 'username ASC', 
			),
		));
	}
		
	public function validateEditSelf($attribute,$params){
		if($attribute == 'curpasswd') {
			$user= Users::model()->find('username=:a', array('a'=>Yii::app()->user->name));
			if ($user->password != $this->curpasswd) {
				$message=Yii::t('yii','Неверный пароль');
				$this->addError($attribute,$message);				
			}
		}
		else if ($attribute == 'newpasswd2') {
			if ($this->newpasswd != $this->newpasswd2) {
					$message=Yii::t('yii','Неверное подтверждение пароля');
					$this->addError($attribute,$message);
			}
			else $this->password = $this->newpasswd;
		}	
	}
	
	public function validateCheckPassword($attribute,$params){
		if ($attribute == 'passwd2') {
			if ($this->passwd != $this->passwd2) {
					$message=Yii::t('yii','Неверное подтверждение пароля');
					$this->addError($attribute,$message);
			}
			else $this->password = $this->passwd;
		}	
	}
	
	public function roleNames() {
		static $res = array();
		if (count($res) == 0) {
			foreach (Yii::app()->authManager->getAuthItems(2) as $k => $v) 
				$res[$k] = $v->description ? $v->description : $k;
		}
		return $res;	
	}
	
	public function roleRights($role) {
        $auth = Yii::app()->authManager;
		$res = array();
		foreach ($auth->getAuthItems(2)[$role]->children as $x) 
			$res[] = $x->name;
			
		$items = $auth->getAuthItems(0);
		$cnt = 1;
		while ($cnt > 0) {
			$cnt = 0;		
			foreach ($items as $x1)
				if (in_array($x1->name, $res))
					foreach ($x1->children as $x2) 
						if (!in_array($x2->name, $res)) {
							$res[] = $x2->name;
							$cnt++;
						}
		}			
		return $res;
	}
	
	public function rightDefs() {
		if (count($this->rightDefs) > 0)
			return $this->rightDefs;
        $auth = Yii::app()->authManager;
		foreach ($auth->getAuthItems(0) as $k => $v) 
			$this->rightDefs[$k] = array(
				'description' => $v->description ? $v->description : $k,
				'checked' => false, 
				'readonly' => false,
				'visible' => true,			  
			);								
		return $this->rightDefs;
	}	
	
	protected function afterFind()
	{
		parent::afterFind();
        $auth = Yii::app()->authManager;
		$this->role = null;
		if ($this->scenario = 'search') {
			foreach ($auth->getAuthAssignments($this->id) as $x)
				if (isset($this->roleNames()[$x->itemName])) {
					$this->role = $x->itemName;
					break;
				}					
			$this->rights = array();
			$this->rightDefs = $this->rightDefs();
			if (isset($this->role)) {
				$roleRights = $this->roleRights($this->role);			
				foreach ($roleRights as $x) {
					$this->rightDefs[$x]['checked'] = true;
					$this->rightDefs[$x]['readonly'] = true;
					$this->rights[] = $x;
				}		
			}				
				
			foreach ($auth->getAuthAssignments($this->id) as $x) {
				if (isset($this->rightDefs[$x->itemName])) 
					$this->rights[] = $x->itemName;
			}
		}
	}
	
	protected function afterSave()
	{
		if (($this->scenario == 'create') or ($this->scenario == 'update')) {
			$auth = Yii::app()->authManager;
			foreach (Yii::app()->authManager->getAuthAssignments($this->id) as $x) 
				$auth->revoke($x->itemName, $this->id);
			if (isset($this->roleNames()[$this->role]))
				$auth->assign($this->role, $this->id);
			/*
			if ($this->rights)
				foreach ($this->rights as $x) 
					$auth->assign($x, $this->id);						
			*/
		}
		parent::afterSave();	
	}  	
	

}