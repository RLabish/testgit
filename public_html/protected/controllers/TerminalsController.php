<?php

class TerminalsController extends Controller
{
  /**
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   * using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  public $layout='//layouts/column2';

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
            'actions'=>array('Config', 'StartTransaction', 'CommitTransaction', 'SetRealState', 
            		'Date', 'Cards', 'SetTransaction', 'State', 'Download', 'Upload'),
		),
	  
		array('allow',
			  'users'=>array('@'),
		),
		
		array('deny',  // deny all users
		  	  'users'=>array('*'),
		),	  
    );
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
 public function actionCreate()
  {
	if (!Yii::app()->user->checkAccess('admin'))
		throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));

	$model=new Terminals;

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if(isset($_POST['Terminals']))
    {
      $model->attributes=$_POST['Terminals'];
      if($model->save())
        $this->redirect(array('index','id'=>$model->id));
    }

    $this->render('create',array(
      'model'=>$model,
    ));
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate($id)
  {
	if (!Yii::app()->user->checkAccess('admin'))
		throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
	  
    $model=$this->loadModel($id);

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if(isset($_POST['Terminals']))
    {
      $model->attributes=$_POST['Terminals'];
      if($model->save())
        $this->redirect(array('index','id'=>$model->id));
    }

    $this->render('update',array(
      'model'=>$model,
    ));
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id the ID of the model to be deleted
   */
  public function actionDelete($id)
  {
		if (!Yii::app()->user->checkAccess('admin'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
		
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
  }

  /**
   * Lists all models.
   */
  public function actionIndex()
  {
	if (!Yii::app()->user->checkAccess('admin'))
		throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
	  
	$model=new Terminals('search');			
	$model->unsetAttributes();  // clear any default values
	if(isset($_GET['Terminals']))
		$model->attributes=$_GET['Terminals'];
	$this->render('index',array(
		'model'=>$model,
	));
  }


  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer the ID of the model to be loaded
   */
  public function loadModel($id)
  {
    $model=Terminals::model()->findByPk((int)$id);
    if($model===null)
      throw new CHttpException(404,'The requested page does not exist.');
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param CModel the model to be validated
   */
  protected function performAjaxValidation($model)
  {
    if(isset($_POST['ajax']) && $_POST['ajax']==='terminals-form')
    {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }
  
	public function actionTerminalsByAzs() {
		if (!Yii::app()->request->isAjaxRequest) return;
		$azsId = Yii::app()->getRequest()->getParam('azsId');
		$criteria = new CDbCriteria;
		if ($azsId) $criteria->compare('azsId',  $azsId);
		$criteria->order = 'name';
		$terminals = Terminals::model()->findAll($criteria);
		$result = array();
		foreach($terminals as $x) {
			$result[] = array('id'=>$x->id, 'label'=>$x->name);
		}
		echo CJSON::encode($result);
		Yii::app()->end();
	}
	

  public function terminalAuth() {
	if (isset($_GET['id'])) { 
		$this->terminal=Terminals::model()->find("active=1 and sn=:sn", array(':sn'=>$_GET['id']));
		if (!isset($this->terminal)) {
			$this->terminal=Terminals::model()->find("active=1 and sn=:sn", array(':sn'=>'00000'));
			if (isset($this->terminal)) {
				$this->terminal->sn = $_GET['id'];
				$this->terminal->save();
			}
			if (!isset($this->terminal) && (Yii::app()->theme->name == 'test')) {
				$tm = new Terminals();
				$tm->azsId = 1;
				$tm->login = $_GET['id'];
				$tm->psw = ' ';
				$tm->name = $_GET['id'];
				$tm->sn = $_GET['id'];
				$tm->active = 1;
				$tm->transNo = 0;
				$tm->eventNo = 0;
				if (!$tm->save())
					throw new Exception($tm->getErrors());
				$tm->id = Yii::app()->db->lastInsertID;	
				$this->terminal = $tm;
			}			
		}
	}
	else 
		$this->terminal=Terminals::model()->find("active=1 and login=:login and psw=:psw", array(':login'=>$_GET['user'], ':psw'=>$_GET['psw']));
		
    if (!isset($this->terminal))
      throw new CHttpException(403, Yii::t('yii','Authenticate fail!'));
    Yii::log('AUTH: OK; TERMINAL [name "'.$this->terminal->name.'", sn '.$this->terminal->sn.']');
    $this->terminal = $this->terminal;
}

  public function actionDate()
  {
    Yii::log('### TERMINAL.Date');
    $this->terminalAuth();
    $s = Yii::app()->DateFormatter->format('dd.MM.yyyy HH:mm:ss', time()).'#';
    echo $s;
    Yii::log('date='.$s);
    Yii::log('####################');
  }

  public function actionCards()
  {
    Yii::log('### TERMINAL.Cards');
    $this->terminalAuth();

    $fuels = Fuels::model()->findAll();
    $now = time();

    $criteria=new CDbCriteria;
    $criteria->order = 'id';
    if (isset($_GET['datefrom'])) {
      $criteria->compare('update_date','>='.Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $_GET['datefrom']));
    }
    $cards = Cards::model()->findAll($criteria);
    $lines = 0;

    foreach ($cards as $c) {
    	if ($c->state == 1)
      		$type = 1;
    	else if ($c->state == 0) {
    		if ($c->type == 1)
    			$type = 10;
    		else if ($c->type == 2)
    			$type = 11;
    		else
    			$type = 0;
    	}
    	else
      		$type = $c->state;

      $s = $c->id.';'.$c->number.';'.$type.';'.Yii::app()->DateFormatter->format('dd.MM.yyyy', $c->expire).';';
      $i = 0;
      foreach ($fuels as $f) {
        $v = $c->balanceByFuelId($f->id);
        if ($v < 0)
          $v = 0;
        $i++;
        if (($i == 1) || ($v > 2)) {
        	echo $s.'1;'.$f->code.';'.round($v*100).';0;0#'."\n";
        	$lines++;
        }
      }
//      if ($i == 0) {
//        echo $s."0;0;0;0;0#\n";
//        $lines++;
//      }
    }
    echo '$;'.$lines.';'. Yii::app()->DateFormatter->format('dd.MM.yyyy HH:mm:ss', $now).';#'."\n";
    Yii::log('####################');

  	$this->terminal->syncDate = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());
  	if (!$this->terminal->save()) Yii::log(print_r($this->terminal->errors, true), 'error');
	
    $event = new TerminalEvents();
	$event->terminalId = $this->terminal->id;
	$event->eventNo = 0;
	$event->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());
	$event->type = 0;
	$event->msg = 'Синхронизация';
    if (!$event->save()) Yii::log(print_r($event->getErrors(), true));
  }

  public function actionSetTransaction()
  {
    Yii::log('### TERMINAL.SetTransaction');
    $this->terminalAuth();

    $date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $_GET['date']);
    $cardNo = "0000".$_GET['cardNo'];
    $cardNo2 = "0000".$_GET['cardNo2'];

    $fuelNo = $_GET['fuelNo'];
    $volume = $_GET['volume']/100;
    $pumpNo = $_GET['pumpNo'];
    $nozzleNo = $_GET['nozzleNo'];
    $tankNo = $_GET['tankNo'];
    $counter = $_GET['counter']/100;

	$mc = new Cards;
    $card=$mc->find("number=:x", array(':x'=>$cardNo));
    $card2=$mc->find("number=:x", array(':x'=>$cardNo2));
    $fuel = Fuels::model()->find("code=:x", array(':x'=>$fuelNo));
    $pump=Pumps::model()->find("terminalId=:t and pumpNo=:p and nozzleNo=:n", array(':t'=>$this->terminal->id, ':p'=>$pumpNo, ':n'=>$nozzleNo,));

    $criteria=new CDbCriteria;
    $criteria->with = 'oper';
    $criteria->compare('terminalId', $this->terminal->id);
    $criteria->compare('oper.date', $date);
    $trans = PumpTransactions::model()->find($criteria);

    if (isset($trans)) {
      echo "OK#";
      Yii::log('####################');
      return;
    }
	$dcounter = $counter - ($pump->counter + $volume);
    if (($dcounter > 0.001) && ($dcounter < 99999)) {
      $v0 = $counter - $pump->counter - $volume;
      $oper=CardOperations::model();
      $oper->isNewRecord = true;
      $oper->id = null;
      $oper->operationType = 3;
      $oper->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $date);
      $oper->cardId = 0;
      $oper->fuelId = $fuel->id;
      $oper->volume = -$v0;
      $oper->balance = 0;
      $oper->state = 0;
      $oper->save();
      $oper->id = Yii::app()->db->lastInsertID;

      $trans = PumpTransactions::model();
      $trans->isNewRecord = true;
      $trans->operId = $oper->id;
      $trans->state = 0;
      $trans->terminalId = $this->terminal->id;
      $trans->fuelId = $fuel->id;
	  if (isset($tank))
  		$trans->tankId = $tank->id;
  	  else
  		$trans->tankId = null;  		
  	  if (isset($pump)) {
  		$trans->pumpId = $pump->id;
        $trans->counterBegin = $pump->counter;
        $trans->counterEnd = $counter - $volume;		
	  }
  	  else {
  		$trans->pumpId = null;
        $trans->counterBegin = null;
        $trans->counterEnd = null;		
	  }	  
      $trans->save();
    }
    
    $oper= new CardOperations();
    $oper->isNewRecord = true;
    $oper->id = null;
    $oper->operationType = 2;
    $oper->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $date);
    $oper->cardId = $card->id;
	if (isset($card2))
		$oper->autoCardId = $card2->id;
    $oper->fuelId = $fuel->id;
    $oper->volume = -$volume;
    $oper->balance = $card->balanceByFuelId($fuel->id) - $volume;
    $oper->state = 0;
    $oper->save();
    $oper->id = Yii::app()->db->lastInsertID;

    $trans = new PumpTransactions();
    $trans->isNewRecord = true;
    $trans->operId = $oper->id;
    $trans->state = 0;
    $trans->terminalId = $this->terminal->id;
    $trans->pumpId = $pump->id;
    $trans->counterBegin = $counter - $volume;
    $trans->counterEnd = $counter;
    $trans->tankId = $pump->tank->id;
    $trans->fuelId = $fuel->id;
    $trans->save();
    if (!$trans->save()) {
		Yii::log(print_r($trans->getErrors(), true));
	};

    $pump->counter = $counter;
    if (!$pump->save()) {
		Yii::log(print_r($pump->getErrors(), true));
	};

    $balance = CardBalance::model()->find("cardId=:c and fuelId=:f", array(':c'=>$card->id, ':f'=>$fuel->id,));
	if (isset($balance)) {
		$balance->volume = $balance->volume - $volume;
		$balance->save();
	}

    $card->wasChanged();
    $card->save();

    echo "OK#";
     Yii::log('####################');
  }
  
  public function actionSetRealState()
  {
    Yii::log('### TERMINAL.SET_REAL_STATE');
    $this->terminalAuth();
    $tankNo = $_GET['n'];
    $tank=Tanks::model()->find('terminalId=:terminalId and number=:number', array('terminalId'=>$this->terminal->id,'number'=>$tankNo,));
    if (!isset($tank))
      throw new CHttpException(400, Yii::t('yii','Tank not found!'));

    $tankId = $tank->id;
    $status = $_GET['err'];
    $fuelLevel = isset($_GET['Lf']) ? $_GET['Lf'] * 0.1 : NULL;
    $fuelVolume = isset($_GET['Vf']) ? $_GET['Vf'] : NULL;
    if ($fuelVolume < 0)
      $fuelVolume = 65535 + $fuelVolume;
    $fuelMass = isset($_GET['Mf']) ? $_GET['Mf'] : NULL;
    $temperature = isset($_GET['T']) ? $_GET['T'] * 0.1 : NULL;
    $density = isset($_GET['P']) ? $_GET['P'] * 0.0001 : NULL;
    $waterLevel = isset($_GET['Lw']) ? $_GET['Lw'] : NULL;
    $waterVolume = isset($_GET['Vw']) ? $_GET['Vw'] : NULL;
    Yii::log('PARAMS: ERR='.$status.'; Hf='.$fuelLevel.'; Vf='.$fuelVolume.'; Mf='.$fuelMass.'; T='.$temperature.'; P='.$density.'; Hw='.$waterLevel.'; Vw='.$waterVolume);

    $connection = Yii::app()->db;
    $transaction = $connection->beginTransaction();
    try {
      $sql = 'call tankSetRealState(:in_tankId, null, :in_status, :in_fuelLevel, :in_fuelVolume, :in_fuelMass, :in_temperature, :in_density, :in_waterLevel, :in_waterVolume)';
      $command = $connection->createCommand($sql);
      $command->bindValues(array (
          'in_tankId'=>$tankId,
          'in_status'=> $status,
          'in_fuelLevel'=> $fuelLevel,
          'in_fuelVolume'=> $fuelVolume,
          'in_fuelMass'=> $fuelMass,
          'in_temperature'=> $temperature,
          'in_density'=> $density,
          'in_waterLevel'=> $waterLevel,
          'in_waterVolume'=> $waterVolume
      ));
      $command->execute();
      $transaction->commit();
    } catch(Exception $e) {
      $transaction->rollBack();
      throw $e;
    }
    echo "OK";
    Yii::log('####################');
  }

  public function actionState()
  {
  	Yii::log('### TERMINAL.State');
  	$this->terminalAuth();
	
  	if (isset($_GET['ussd'])) {
		$event = new TerminalEvents();
		$event->terminalId = $this->terminal->id;
		$event->eventNo = 0;
		$event->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time()); 
		$event->type = TerminalEvents::TYPE_SYNC;
		Yii::log('USSD: '.$_GET['ussd']);
  		$event->msg = 'USSD: '.$_GET['ussd'];
		$event->save();
  	}	
  	Yii::log('####################');
  }

  public function actionDownload()
  {
   	Yii::log(' ');
   	Yii::log('### TERMINAL.Download');
  	$this->terminalAuth();
  	if (isset($_GET['datefrom']))
  		Yii::log("DATE FROM: ".$_GET['datefrom']);
  	if (isset($_GET['cardver']))
  		Yii::log("CARD VERSION FROM: ".$_GET['cardver']);
		
	$workpath = Yii::app()->runtimePath.'/terminals';

	if (!is_dir($workpath))
		mkdir($workpath, 0777, true);
  	$filename = $this->terminal->name."-download-".date_format(date_create(), 'ymdHis').".txt"; 	
  	Yii::log('file='.$filename);
  	$fp = fopen("$workpath/$filename", "a");
	if (!$fp)
		throw new CHttpException(500,'error open file '.$filename);
	$line = 1;
	if (isset($this->terminal->dbg2902) && ($this->terminal->dbg2902 != 0)) {
	}
	else
		fwrite($fp, $line++.";D;".Yii::app()->DateFormatter->format('dd.MM.yyyy HH:mm:ss', time())."\n"); 
  	fwrite($fp, $line++.";N;".$this->terminal->transNo."\n");
  	fwrite($fp, $line++.";t;".$this->terminal->transNo."\n");
  	fwrite($fp, $line++.";e;".$this->terminal->eventNo."\n");
  	
  	$fuels = Fuels::model()->findAll();
  	$now = time();
	
	if  (Yii::app()->theme->name == 'clients') {	
		$criteria=new CDbCriteria;
		$criteria->order = 't.id';
		$criteria->with = array('organizationbalances');		
		if (isset($_GET['cardver'])) {
			$criteria->compare('t.version','>'.$_GET['cardver']);
		}  	
		foreach (Organizations::model()->findAll($criteria) as $org) {
			$cardnumber = '0000FFF1'.sprintf('%04X', $org->id);
			$cardowner = substr(iconv("UTF-8", "windows-1251", $org->name), 0, 16);		
			fwrite($fp, $line++.";C;{$cardnumber};0; ;{$cardowner};0;1.1.2100;{$org->version}\n");
	  		foreach ($fuels as $f) {
  				$v = $org->balanceByFuelId($f->id);
  				if ($v < 2) continue;
  				fwrite($fp, $line++.";L;0;1.1.2000;1.1.2100;".$f->code.';'.round($v*100).";0;0\n");
  			}
  	  	}
	}
	
  	$criteria=new CDbCriteria;
  	$criteria->order = 't.id';
  	$criteria->compare('t.id','>0');
  	if (isset($_GET['datefrom'])) {
  		$criteria->compare('t.update_date','>='.Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $_GET['datefrom']));
  	}
  	if (isset($_GET['cardver'])) {
  		$criteria->compare('t.version','>'.$_GET['cardver']);
  	} 

	if  (Yii::app()->theme->name == 'test') {
		$criteria->compare('t.description',"test"); 
	} 	

	if  (Yii::app()->theme->name == 'clients') {
		$criteria->with = array('organization');
	} 	
			
	$cards = Cards::model()->findAll($criteria);
	
  	$i = 0;
  	foreach ($cards as $c) {
		if (Yii::app()->theme->name == 'test') {
			$c->version = 1;
		}

  		// <line>;C;<card_no>;<card_type>;<pin>;<owner>;<state>;<expire>  		
		$pin = $c->pin;
		if (($pin == NULL) || (strlen($pin) == 0)) 
			$pin = ' ';
//		$owner = substr(iconv("UTF-8", "windows-1251", $c->owner), 0, 16);
		$owner = substr(mb_convert_encoding($c->owner, 'windows-1251', 'UTF-8'), 0, 16);
		
		$owner = str_replace(';', ' ', $owner);
		$version = $c->version;

  		fwrite($fp, $line++.";C;{$c->number};{$c->ownerType};$pin;$owner;{$c->state};".Yii::app()->DateFormatter->format('dd.MM.yyyy', $c->expire).";$version\n");
  	  	if ($c->type == Cards::TYPE_LIMITED) {
  	  		$limits = $c->limits;
  			foreach ($limits as $lim) {
  			// <line>;L;<limit_type;<date_begin>;<date_end>;<fuel_code>;<order_volume>;<used_volume>;<last_sale_date>
  				fwrite($fp, $line++.";L;".$lim->limitType.";1.1.2000;1.1.2100;".$lim->fuel->code.';'.round($lim->orderVolume*100).';'.round($lim->usageVolume*100).';'.$lim->lastSaleDate->format('d.m.Y H:i:s')."\n");  	
  			}
  		}
  	  	else if ($c->type == Cards::TYPE_DEBIT) {
	  		foreach ($fuels as $f) {
  				$v = $c->balanceByFuelId($f->id);
  				if ($v < 2) continue;
  				// <line>;L;<limit_type;<date_begin>;<date_end>;<fuel_code>;<order_volume>;<used_volume>;<last_sale_date>
  				fwrite($fp, $line++.";L;0;1.1.2000;1.1.2100;".$f->code.';'.round($v*100).";0;0\n");

  			}
  	  	}
  	  	else if (($c->type == Cards::TYPE_SERVICE) || ($c->type == Cards::TYPE_MOVE)|| ($c->type == Cards::TYPE_AUTO_CALIBR)) {		
  	  		foreach ($fuels as $f) {
  	  			// <line>;L;<limit_type;<date_begin>;<date_end>;<fuel_code>;<order_volume>;<used_volume>;<last_sale_date>
  	  			fwrite($fp, $line++.";L;3;1.1.2000;1.1.2100;".$f->code.";99999999;0;0\n");
  	  		}
  	  	}
		if  (Yii::app()->theme->name == 'clients') {	
			if (isset($c->orgId)) {
				$orgcode = 'FFF1'.sprintf('%04X', $c->orgId);
				fwrite($fp, $line++.";L;10;{$orgcode};\n");  	
			}
		}		
  	}
  	if (!fwrite($fp, $line++.";#\n")) 
  		throw new CHttpException(500,'ERROR write to file '.$filename);
  	fclose($fp);
  	
  	$fp = fopen("$workpath/$filename", "r"); 
  	if ($fp)
  		while (!feof($fp))
  			echo fgets($fp);
  	fclose($fp);
  	
  	Yii::log('####################');
  }
  
  private $terminal;
  
  public function actionUpload()
  {
  	Yii::log(' ');
  	Yii::log('### TERMINAL.Upload');
  	$this->terminalAuth();
	$workpath = Yii::app()->runtimePath.'/terminals';
  	if (!is_dir($workpath))
  		mkdir($workpath, 0777, true);
  	$filename = $this->terminal->name."-upload-".date_format(date_create(), 'ymdHis').".txt"; 	
  	Yii::log('file='.$filename);

	if (isset($_FILES['userfile'])) {
		if (!move_uploaded_file($_FILES['userfile']['tmp_name'], "$workpath/$filename"))
			throw new CHttpException(500, Yii::t('yii', 'Uploaded file not found!'));
	}
	else {
		$fp = fopen("$workpath/$filename", "a");
		fwrite($fp,file_get_contents("php://input"));
		fclose($fp);
	}
		
  	$fp = fopen("$workpath/$filename", "r");
  	if (!$fp)
  		throw new CHttpException(500,'error open file '.$filename);
  			
  	$line = 0;
  	$complete = false;
  	try {  		
		while (!$complete && !feof($fp)) {
			$s = fgets($fp);	
			Yii::log('line: '.substr($s, 0, -1));			
			$eot = ";\n";		
			$tok = strtok($s, $eot);
			if (++$line != $tok)	
  				throw new Exception("bad line number $tok");
			$cmd = strtok($eot);
			//echo "-> $this->fline: $cmd \n";
			switch ($cmd) {
				case ("#"): {
					$complete = true;
					break;
				}
				case ("T"): {
					$this->cmd_set_transaction();
					break;
				}			
				case ("P"): {
					$this->cmd_set_counters();
					break;
				}				
				case ("R"): {
					$this->cmd_set_tankstate();
					break;
				}
				case ("E"): {
					$this->cmd_set_event();
					break;
				}			
				default: {
					Yii::log( "line $line: unknown command <$cmd>", 'warning');
				}
			}
		}
		if (!$complete)
			throw new CHttpException(400,"expected EOC command (#)");
		
	} catch (Exception $e) {
  		fclose($fp);
  		$s = "[file $filename, line $line] \n".$e->getMessage();
  		
  		$fn = "$workpath/$filename.err";
  		$fp = fopen($fn, "a");
  		fwrite($fp,$s);
  		fclose($fp);  		

  		$sync->message = $e->getMessage();
  		$sync->save();
  		
  		throw new CHttpException(400, $s);		
	}	
  	fclose($fp);
  	
  	$this->terminal->syncDate = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());
  	if (!$this->terminal->save())
  		Yii::log(print_r($this->terminal->errors, true), 'error');
  	
  	$event = new TerminalEvents();
  	$event->terminalId = $this->terminal->id;
  	$event->eventNo = 0;
  	$event->date = $this->terminal->syncDate; 
  	$event->type = TerminalEvents::TYPE_SYNC;
  	$event->msg = 'Синхронизация';
  	if (!$event->save())
  		Yii::log(print_r($event->errors, true), 'error');
  	
  	Yii::log('####################');
  }

  private function cmd_set_transaction() {
  	$eot = ";\n";  	
  	$no = strtok($eot);
  	$date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', strtok($eot));
  	$oper_id = (int)strtok($eot);
  	$card_no = substr('000000000000'.strtok($eot), -12);
  	$lim_idx = strtok($eot);
  	$fuel_code = strtok($eot);
  	$volume = (int)strtok($eot)/100;
  	$pump_no = (int)strtok($eot);
  	$nozzle_no = (int)strtok($eot);
  	$tank_no = (int)strtok($eot);
	$card2_no = substr('000000000000'.strtok($eot), -12);
	if ($no == 0) $no = $this->terminal->transNo + 1;	
  	if ($no != ($this->terminal->transNo + 1)) {  	
//  		throw new Exception("bad transaction number $no, required ".($this->terminal->transNo+1));
		Yii::log("bad transaction number $no, required ".($this->terminal->transNo+1), "warning");
		return;
	}
	$mc = new Cards;
	$card=$mc->find("number like :x", array(':x'=> '%'.substr($card_no, -8)));
  	if (!isset($card)) {
  		$card = new Cards;
  		$card->isNewRecord = true;
  		$card->id = null;
		if  (Yii::app()->theme->name == 'clients') {
			$card->orgId = null;
		}
  		$card->number = $card_no;
  		$card->state = 1;
  		$card->typeExt = 3;
  		$card->expireFmt = '01.01.2000';
  		$card->ownerType = Cards::OWNER_TYPE_CUSTOM;
  		$card->owner = '*';
  		if (!$card->save()) throw new Exception(print_r($card->getErrors(), true));
  		$card->id = Yii::app()->db->lastInsertID;
  	}
	if ($card-> id == 0) {
		if  (Yii::app()->theme->name == 'clients') {
			$card->orgId = null;
		}
	}
	if (isset($card2_no) && (trim($card2_no) != '') && ($card2_no != '000000000000')) {
		$mc = new Cards;
//		$card2=$mc->find("number=:x", array(':x'=>$card2_no));	
		$card2=$mc->find("number like :x", array(':x'=> '%'.substr($card2_no, 4)));

		if (!isset($card2)) {
			$card2 = new Cards;
			$card2->isNewRecord = true;
			$card2->id = null;
			$card2->number = $card2_no;
			$card2->state = 1;
			$card2->typeExt = 3;
			$card2->expireFmt = '01.01.2000';
			$card2->ownerType = Cards::OWNER_TYPE_AUTO;
			$card2->owner = '???';
			if (!$card2->save()) throw new Exception($card->getErrors());
			$card2->id = Yii::app()->db->lastInsertID;
		}		
	}
  	$pump = Pumps::model()->find("terminalId=:t and pumpNo=:p and nozzleNo=:n", array(':t'=>$this->terminal->id, ':p'=>$pump_no, ':n'=>$nozzle_no,));
	if ($tank_no == 0) {
		if (isset($pump)) $tank = $pump->tank;
	}
	else {
		$tank = Tanks::model()->find("terminalId=:t and number=:x", array(':t'=>$this->terminal->id, ':x'=>$tank_no));
		if (!isset($tank)) $tank = Tanks::model()->find("azsId=:t and number=:x", array(':t'=>$this->terminal->azs->id, ':x'=>$tank_no));		
	}
  	if (isset($pump) && isset($tank) && ($tank->id != $pump->tankId)) {
  		$pump->tankId = $tank->id;
  		if (!$pump->save())	Yii::log(print_r($pump->errors, true), 'error');
  	}
	if ($fuel_code == 0) $fuel = $tank->fuel;
	else $fuel = Fuels::model()->find("code=:x", array(':x'=>$fuel_code));
  	if (!isset($fuel))	throw new Exception("unknown fuel code $fuel_code");
  	
  	$oper= new CardOperations;
  	$oper->isNewRecord = true;
  	$oper->id = null;
  	if ($card->type == Cards::TYPE_SERVICE)
  		$oper->operationType = CardOperations::TYPE_PUMP_SERVICE; // техпролив
	else if ($card->type == Cards::TYPE_MOVE)
  		$oper->operationType = CardOperations::TYPE_PUMP_MOVE; // перемещение
  	else if ($oper_id == 1)
  		$oper->operationType = CardOperations::TYPE_PUMP_SALE; // отпуск топлива
  	else if ($oper_id == 9)
  		$oper->operationType = CardOperations::TYPE_PUMP_ALARM; // автономный пролив
  	else
  		throw new Exception("unknown operation $oper_id\n");
  	$oper->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $date);
  	$oper->state = 0;
  	$oper->fuelId = $fuel->id;
	if ($volume > 999999.00) $volume = 99999.00;
  	$oper->volume = -$volume;
		
	if (Yii::app()->params['card_ident_def'] == 2) {
		if (isset($card2)) {
			$oper->cardId = $card->id;	
			$oper->autoCardId = $card2->id;				
			$oper->balance = $card2->balanceByFuelId($fuel->id) - $volume;
		}
		else {
			$oper->cardId = null;	
			$oper->autoCardId = $card->id;				
			$oper->balance = $card->balanceByFuelId($fuel->id) - $volume;
		}		
	}
	else {
	  	$oper->cardId = $card->id;
		$oper->balance = $card->balanceByFuelId($fuel->id) - $volume;
		if  (Yii::app()->theme->name == 'clients') {
			if (isset($card->orgId)) $oper->orgId = $card->orgId;
			else $oper->orgId = null;
		}
		if (isset($card2))	$oper->autoCardId = $card2->id;		
	}
 	if (!$oper->save()) throw new Exception($oper->getErrors());
 	$oper->id = Yii::app()->db->lastInsertID;
  	
  	$trans = PumpTransactions::model();
  	$trans->isNewRecord = true;
  	$trans->operId = $oper->id;
  	$trans->state = 0;
  	$trans->terminalId = $this->terminal->id;
  	$trans->fuelId = $fuel->id;
  	if (isset($tank))
  		$trans->tankId = $tank->id;
  	else
  		$trans->tankId = null;  		
  	if (isset($pump))
  		$trans->pumpId = $pump->id;
  	else
  		$trans->pumpId = null;
  	$trans->transNo = $no;  		
  	if (!$trans->save()) {
	Yii::log(print_r($trans->getErrors(), true));
  		throw new Exception($trans->getErrors());
		}
  	
  	$this->terminal->transNo++;
  	if (!$this->terminal->save())
  		throw new Exception($this->terminal->getErrors());
  	
	$balance = CardBalance::model()->find("cardId=:c and fuelId=:f", array(':c'=>$card->id, ':f'=>$fuel->id,));
	if (isset($balance)) {
    		$balance->volume = $balance->volume - $volume;
	    	$balance->save();
    }
	if  (Yii::app()->theme->name == 'clients') {
		if (isset($card->orgId)) {
			$balance = OrganizationBalance::model()->find("orgId=:a and fuelId=:b", array(':a'=>$card->orgId, ':b'=>$fuel->id,));
			if (isset($balance)) {
				$balance->volume = $balance->volume - $volume;
				if (!$balance->save()) {
					$this->terminal->active = false;
					$this->terminal->save();					
					throw new Exception($balance->getErrors());					
				};
			}
			else Yii::log("WARNING: no balance for organization ".$card->orgId, "warning");
		}
		else Yii::log("WARNING: no organization for card".$card->id, "warning");
	}	

	$card->wasChanged();
	$card->save();	
	$limits = $card->limits;
	foreach ($limits as $lim) 
		$lim->registerSale($fuel->id, $volume);
	
	if (isset($card2)) {
		$card2->wasChanged();
		$card2->save();	
		$limits = $card2->limits;
		foreach ($limits as $lim) 
			$lim->registerSale($fuel->id, $volume);
	}

		
  	echo "transaction $no saved (id $trans->operId)\n";
  }
  
  private function cmd_set_counters() {
  	$eot = ";\n";  	
  	$pump_no = (int)strtok($eot);
  	$nozzle_no = 1;
  	$s = strtok($eot);
  	while ($s != NULL) {
  		$counter = (int)$s;
	  	$pump = Pumps::model()->find("terminalId=:t and pumpNo=:x and nozzleNo=:n",
	  			array(':t'=>$this->terminal->id, ':x'=>$pump_no, ':n'=>$nozzle_no++));
	  	if (isset($pump)) {
	  		$pump->counter = $counter / 100;
	  		if (!$pump->save())
	  			throw new Exception($realState->getErrors());
	  	}
  		$s = strtok($eot);
	 }  	
  }
  
  private function cmd_set_tankstate() {
  	$eot = ";\n";  	
  	$tank_no = (int)strtok($eot);  	
  	$tank = Tanks::model()->find("terminalId=:t and number=:x", array(':t'=>$this->terminal->id, ':x'=>$tank_no));  	
  	if (!isset($tank)) {
  		Yii::log("tank #$tank_no not exists", "warning");
  		return;  		
  	}
	if (isset($tank->ignoreLevelMeter) && $tank->ignoreLevelMeter) {
  		Yii::log("tank #$tank_no real state setting is lock", "warning");
  		return;  			
	}
	
	$realState = TankRealStates::model();
	$realState->isNewRecord = true;
	$realState->id = null;
	$realState->tankId = $tank->id;
  	$realState->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());
  	$realState->status = (int)strtok($eot);
  	if ($realState->status == 0) {  		
  		$realState->fuelLevel = (int)strtok($eot) / 10;
  		$realState->fuelVolume = (int)strtok($eot);
  		$realState->temperature = (int)strtok($eot) / 10;
  		$realState->density = (int)strtok($eot) / 10000;
  		$realState->fuelMass = (int)strtok($eot);
		if (isset($tank->ignoreWater)) {
			$realState->waterLevel = 0;
			$realState->waterVolume =  0;  					
		}
		else {
			$realState->waterLevel = (int)strtok($eot) / 10;
			$realState->waterVolume =  (int)strtok($eot);  		
		}
  	}
  	else {
  		$realState->fuelLevel = NULL;
  		$realState->fuelVolume = NULL;
  		$realState->temperature = NULL;
  		$realState->density = NULL;
  		$realState->fuelMass = NULL;
  		$realState->waterLevel = NULL;
  		$realState->waterVolume =  NULL;  		
  	}
  	if (!$realState->save()) 
  		throw new Exception($realState->getErrors());
	$realState->id = Yii::app()->db->lastInsertID;
	$tank->realStateId = $realState->id;
	$tank->save();  	
  }

private function cmd_set_event() {
	$eot = ";\n";
	$event = new TerminalEvents();
	$event->terminalId = $this->terminal->id;
	$event->eventNo = (int)strtok($eot);
	$event->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', strtok($eot));
	$event->type = (int)strtok($eot);
	
	if ($event->eventNo < ($this->terminal->eventNo + 1)) {
		Yii::log("bad event number $event->eventNo, required ".($this->terminal->eventNo+1), "warning");
		return;
	}
	
	$event->sysInfo = '';
	$s = strtok($eot);
	$params = array();
	while ($s != NULL) {
		$params[] = $s;
		$event->sysInfo = $event->sysInfo.$s.';';
		$s = strtok($eot);
	}
	/*
	Yii::log('cmd_set_event:: event='.$event->eventNo);
	Yii::log("cmd_set_event::\n"
			.'no:      '.$event->eventNo."\n"
			.'type:    '.$event->type."\n"
			.'params:  '.print_r($params, true)."\n"
			.'params:  '.print_r($params, true)."\n"
			.'sysinfo: '.($event->sysInfo).">\n"
	);
	*/
	
	$need_save = true;
	switch ($event->type) {
		case (TerminalEvents::TYPE_STARTUP): {
			$event->msg = 'Включение питания';
			break;
		}
		case (TerminalEvents::TYPE_SHUTDOWN): {
			$event->msg = 'Отключение питания';
			break;
		}
		case (TerminalEvents::TYPE_TANK_SENSOR_STATE): {
			$need_save = false;
			$tank = Tanks::model()->find("terminalId=:t and number=:x", array(':t'=>$this->terminal->id, ':x'=>$params[0]));
			if ($tank == NULL) break;
			$realState = new TankRealStates();
			$realState->isNewRecord = true;
			$realState->tankId = $tank->id;
			$realState->date = $event->date;
			$realState->status = $params[1];
			$realState->fuelLevel = (int)$params[2]/10;
			$realState->fuelVolume = (float)$params[3];
			$realState->temperature = (float)$params[4]/10;
			$realState->density = (float)$params[5]/10000;
			$realState->fuelMass = (float)$params[6];
			$realState->waterLevel = (float)$params[7]/10;
			$realState->waterVolume = (float)$params[8];
		
			if (!$realState->save()) {
				throw new Exception($realState->getErrors());
			}
			$need_save = false;
			break;
		}
		default: {
			$event->msg = '<unknown event>';
		}
	}		
	if ($need_save)
		$event->save();
	$this->terminal->eventNo = $event->eventNo;
	$this->terminal->save();
}

public function actionTest() {
	Yii::log('### TERMINAL.Test');
	$this->terminalAuth();

	$date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());
	$cardNo = "0000".$_GET['cardNo'];
	$fuelNo = $_GET['fuelNo'];
	$volume = $_GET['volume']/100;

	$card=Cards::model()->find("number=:x", array(':x'=>$cardNo));
	$fuel = Fuels::model()->find("code=:x", array(':x'=>$fuelNo));

	$oper=CardOperations::model();
	$oper->isNewRecord = true;
	$oper->id = null;
	$oper->operationType = 2;
	$oper->date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $date);
	$oper->cardId = $card->id;
	$oper->fuelId = $fuel->id;
	$oper->volume = -$volume;
	$oper->balance = $card->balanceByFuelId($fuel->id) - $volume;
	$oper->state = 0;
	$oper->save();
	$oper->id = Yii::app()->db->lastInsertID;

	$trans = PumpTransactions::model();
	$trans->isNewRecord = true;
	$trans->operId = $oper->id;
	$trans->state = 0;
	$trans->terminalId = $this->terminal->id;
	$trans->pumpId = 1;
	$trans->counterBegin = 0;
	$trans->counterEnd = 0;
	$trans->tankId = 1;
	$trans->fuelId = $fuel->id;
	$trans->save();

	$balance = CardBalance::model()->find("cardId=:c and fuelId=:f", array(':c'=>$card->id, ':f'=>$fuel->id,));
	$balance->volume = $balance->volume - $volume;
	echo "$balance->volume ".$balance->volume."<br>";
	if (!$balance->save()) {
		echo "error";
		echo print_r($balance->getErrors());
		echo "<br>";
	}
	else
		echo "balance saved.<br>";

		$card->wasChanged();
		$card->save();

		echo "OK";
	}

}


