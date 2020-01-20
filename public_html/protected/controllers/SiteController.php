<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	public function accessRules()
	{
		return array(
				array('allow',
						'actions'=>array('login, test'),
				),
				array('allow', // allow authenticated users to access all actions
						'users'=>array('@'),
				),
				array('deny'),
				//              array('allow'),
		);
	}
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	
	public function actionTest()
	{
		echo Yii::app()->runtimePath;
		sleep(30);
	}
	
	
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		//$this->render('index');
		$this->redirect($this->createUrl('cards/index',array('type'=>1,)));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$this->layout = '//layouts/empty';		
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function actionCabinet()
	{
		if (!Yii::app()->user->checkAccess('users.cabinet'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
	
		$model= Users::model()->find('username=:a', array('a'=>Yii::app()->user->name));
			Yii::log('AUTH a1 "'.Yii::app()->user->name.'"');
			Yii::log('AUTH a2 "'.$model->id.'"');
		$model->scenario = 'editself';		
		if(isset($_POST['Users']))
		{
			if (isset($_POST['Users']['id']))
					throw new CHttpException(403, Yii::t('yii','Error in POST params.'));			
			Yii::log('AUTH 1');
			$model->attributes=$_POST['Users'];
			Yii::log('AUTH 2');
			if ($model->save()) {
				Yii::log('AUTH 3');
				if ($model->username != Yii::app()->user->name) {
					Yii::app()->user->logout();
					$identity = new UserIdentity($model->username,$model->password);
					$identity->authenticate();
					//$this->redirect(array('cabinet',));
				}
				$this->redirect(Yii::app()->homeUrl);					
				return;							
			}
		}
		$model->password = '';
		$this->render('cabinet',array('model'=>$model,));
	}
	
	
	public function actionPasswd()
	{
		if (!Yii::app()->user->checkAccess('users.passwd'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
		
		$this->layout='//layouts/column2';
		$model= Users::model()->find('username=:a', array('a'=>Yii::app()->user->name));
		if (! isset($model))
			$this->redirect(array('login',));
		
		if(isset($_POST['Users']))
		{
			$model->password2 = $_POST['Users']['password2'];
			$model->newpasswd = $_POST['Users']['newpasswd'];
			$model->newpasswd2 = $_POST['Users']['newpasswd2'];		
			
			$model->username=Yii::app()->user->name;
			if ($model->editpasswd())
				$this->redirect(array('cabinet',));
		}

		$this->render('passwd',array('model'=>$model,));		
	}
		
	public function actionInstall() {            
        echo 'action lock!<br/>';	
		return;
        $auth=Yii::app()->authManager;
        
        //сбрасываем все существующие правила
        $auth->clearAll();
       
        //редактирование справочников
        $auth->createOperation('list.update');
        
        
        //Операции c картами
        $auth->createOperation('cards.update');
        $auth->createOperation('cards.refill');
        //личный кабинет
        $auth->createOperation('users.cabinet');
        $auth->createOperation('users.passwd');
        // док
        $auth->createOperation('fuel.update');
        //настройки        
               
        // роль user
        $role = $auth->createRole('user');
        $role->addChild('users.cabinet');
        $role->addChild('users.passwd');
        
        // роль manager
        $role = $auth->createRole('manager');
        $role->addChild('users.cabinet');
        $role->addChild('users.passwd');
        $role->addChild('list.update');
        $role->addChild('cards.update');
        $role->addChild('cards.refill');
        $role->addChild('fuel.update');        
                
        // роль admin
        $role = $auth->createRole('admin');
        $role->addChild('users.cabinet');
        $role->addChild('users.passwd');
        $role->addChild('cards.update');
        $role->addChild('cards.refill');
        $role->addChild('fuel.update');        
        
        // связь пользователей с ролями 
        $auth->assign('user', 1);   
        $auth->assign('manager', 2);   
        $auth->assign('admin', 3);   
        
        //сохраняем роли и операции
        $auth->save();
        echo 'COMPLETE!<br/>';
    }    
    
    public function actionRebuildOperations() {  	   	
        echo 'action lock!<br/>';	
		return;
    	$connection = Yii::app()->db;
    	$criteria=new CDbCriteria;
    	$criteria->order = 'date';
    	$operations = CardOperations::model()->findAll($criteria);
    	foreach ($operations as $op) {
    		if ($op->operationType == 1) {
    			$sql = 'insert into cardrefill '
    			.'(operId, userId, document)'
    			.' values (:operId,:userId,:document)';
    			$command = Yii::app()->db->createCommand($sql);
    			$command->bindValues(array (
    					'operId'=>$op->id,
    					'userId'=>$op->userId,
    					'document'=>null,
    			));
    			$command->execute();
    		}
    		else {
    			$terminalUserId = $op->userId;
    			$user = Users::model()->find('id=:userId', array('userId'=>$op->userId));
    			$terminal = Terminals::model()->find("login=:login", array(':login'=>$user->username));
    			$terminalId = $terminal->id;
    			
    			$pump = Pumps::model()->find('terminalId', array('terminalId'=>$terminalId));
    			if (!isset($pump)) {
    				$pump = New Pumps();
    				$pump->isNewRecord = true;
    				$pump->terminalId = $terminalId;
    				$pump->pumpNo = 1;
    				$pump->nozzleNo = 1;
    				$pump->save();
    			}
    			$pumpId = $pump->id;
    			$counter = $pump->counter;
    			echo '[PUMP] date: '.$op->date.'; operId:'.$op->id.';userId:'.$op->userId.'; terminalId='.$terminalId.'; pumpId='.$pumpId.', counter='.$counter.', volume='.$op->volume.', sysinfo='.$op->sysInfo.'<br/>';   			
    			
    			$params = array();
    			$tok = strtok($op->sysInfo, ",");
    			while ($tok !== false) {
    				$p = strpos($tok, '=');
    				$params[substr($tok, 0, $p)] = substr($tok, $p + 1);
    				$tok = strtok(",");
    			}
    			if (isset($params['counter1'])) {
    				$counter1 = $params['counter1'];
    				if (isset($counter)) {
    					$v = $counter1 - $counter;
    					if ($v > 0) {
							$dt = strtotime ( '-3 second' , strtotime ($op->date)) ;
							$dt = date('Y-m-j H:i:s', $dt);
    						echo '########## dt: '.$dt.', v: '.$v.'<br/>';
    			
    						$sql = 'insert into cardoperations '
    						.'(operationType,`date`,cardId,fuelId,operation,volume,balance,userId,state,sysInfo)'
    						.' values (:operationType,:date,:cardId,:fuelId,:operation,:volume,:balance,:userId,:state,:sysInfo)';
    						$command = $connection->createCommand($sql);
    						$command->bindValues(array (
    								'operationType'=>3,
    								'date'=>$dt,
    								'cardId'=>0,
    								'fuelId'=>$op->fuelId,
    								'operation'=>'Автономный пролив.Терминал: ID'.$terminalId,
    								'volume'=>-$v,
    								'balance'=>0,
    								'userId'=>$op->userId,
    								'state'=>0,
    								'sysInfo'=>'counter1='.$counter.',counter2='.$counter1,
    						));
    						$command->execute();
    						
    						$operId = $connection->createCommand()
    						->select('max(id) as max')
    						->from(CardOperations::model()->tableName())
    						->queryScalar();
    						
    						
    						$sql = 'insert into pumptransactions '
    						.'(operId, state, terminalId, pumpId, counterBegin, counterEnd)'
    						.' values (:operId,:state,:terminalId,:pumpId,:counterBegin,:counterEnd)';
    						$command = $connection->createCommand($sql);
    						$command->bindValues(array (
    								'operId'=>$operId,
    								'state'=>0,
    								'terminalId'=>$terminalId,
    								'pumpId'=>$pumpId,
    								'counterBegin'=>$counter,
    								'counterEnd'=>$counter1,
    						));
    						$command->execute();
    						
    					}
    				}
    			}
    			if (isset($params['counter2'])) {
    				$counter2 = $params['counter2'];
    				$counter = $counter2;
    				$pumpCounters = PumpCounters::model()->find('pumpId=:pumpId and `date`=cast(:date as date)', array('pumpId'=>$pump->id, 'date'=>$op->date));
    				if (!isset($pumpCounters)) {
    					$pumpCounters = new PumpCounters();
    					$pumpCounters->pumpId = $pump->id;
    					$pumpCounters->date = $op->date;
    					$pumpCounters->counter = 0;
    					$pumpCounters->insert();
    				}
    				$pumpCounters->counter = $counter;
    				$pumpCounters->save();
    			}
    			else 
    				$counter2 = null;
    			
    			$sql = 'insert into pumptransactions '
    			.'(operId, state, terminalId, pumpId, counterBegin, counterEnd)'
    			.' values (:operId,:state,:terminalId,:pumpId,:counterBegin,:counterEnd)';
    			$command = $connection->createCommand($sql);
    			$command->bindValues(array (
    					'operId'=>$op->id,
    					'state'=>0,
    					'terminalId'=>$terminalId,
    					'pumpId'=>$pumpId,
    					'counterBegin'=>$counter1,
    					'counterEnd'=>$counter2,
    			));
    			$command->execute();
    			    			    			
    			$pump->counter = $counter;
    			$pump->save();    			
    		}
    	}
    }    		
	
}