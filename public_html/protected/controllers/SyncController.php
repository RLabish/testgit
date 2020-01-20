<?php

class SyncController extends Controller
{
  /**
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   * using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  private $user;

  /**
   * @return array action filters
   */
  public function filters()
  {
    return array(
      'accessControl', // perform access control for CRUD operations
    );
  }

  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   * @return array access control rules
   */
  public function accessRules()
  {
    return array(
      array('allow',
            'actions'=>array('transactions'),
      ),
      array('deny',  // deny all users
        'users'=>array('*'),
      ),
    );
  }


  private function userAuth() {
		$userName = isset($_GET['user']) ? $_GET['user'] : '';		
		$password = isset($_GET['psw']) ? $_GET['psw'] : '';
		$this->user = Users::model()->find("username=:x", array(':x'=>$userName));
		if (!isset($this->user) || ($this->user->password != $password))
			throw new CHttpException(403, Yii::t('yii','Authenticate fail!'));
		Yii::log('AUTH: OK; SYNC_USER: '.$this->user->username);
 }
	
  public function actionTransactions()
  {
    $this->userAuth();

    $criteria=new CDbCriteria;
    $criteria->order = 't.id';
	if (isset($_GET['id1']))
		$criteria->compare('t.id','>='.$_GET['id1']);
	if (isset($_GET['id2']))
		$criteria->compare('t.id','<='.$_GET['id2']);
//    $criteria->compare('operationType', CardOperations::TYPE_PUMP_SALE);
	$criteria->with =  array('card', 'fuel', 'pumptransaction', 'pumptransaction.terminal', 'pumptransaction.terminal.azs', 
		'pumptransaction.pump', 'pumptransaction.tank', 'autoCard');
    $operations = CardOperations::model()->findAll($criteria);
    $lines = 0;
	$SP = ";";

    foreach ($operations as $op) {
		if (!in_array($op->operationType, array(CardOperations::TYPE_PUMP_SALE, CardOperations::TYPE_PUMP_ALARM, CardOperations::TYPE_PUMP_SERVICE, CardOperations::TYPE_PUMP_MOVE)))
			continue;	
			
		echo $op->id.$SP;
		echo $op->operationType.$SP;
		
		echo Yii::app()->DateFormatter->format('dd.MM.yyyy HH:mm:ss', $op->date).$SP;
		echo $op->card->number.$SP;
		echo $op->card->owner.$SP;
		if (isset($op->autoCard)) {
			echo $op->autoCard->number.$SP;
			echo $op->autoCard->owner.$SP;		
		}
		else {
			echo $SP;
			echo $SP;		
		}
		if (isset($op->pumptransaction->terminal)) {
			echo $op->pumptransaction->terminal->azs->id.$SP;
			echo $op->pumptransaction->terminal->sn.$SP;
		}
		else {
			echo $SP;
			echo $SP;
		}
		if (isset($op->pumptransaction->tank)) 
			echo $op->pumptransaction->tank->number.$SP;
		else 
			echo $SP;
		if (isset($op->pumptransaction->pump)) { 
			echo $op->pumptransaction->pump->pumpNo.$SP;
			echo $op->pumptransaction->pump->nozzleNo.$SP;
		}
		else {
			echo $SP;
			echo $SP;
		}
		echo $op->fuel->code.$SP;
		echo -$op->volume.$SP;	
		
		echo "\n";
	}
  }


}


