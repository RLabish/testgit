<?php

Yii::import('application.extensions.MyUtils');

class ReportsController extends Controller
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
				array('allow', 'users'=>array('@'),),
				array('deny', 'users'=>array('*'),),
		);
	}

	public function actionSales()
	{
		$model = new RptSales('search');
		$model->unsetAttributes();
		if(isset($_GET['RptSales'])) {
			$model->attributes=$_GET['RptSales'];
		}
		else {
			if (Yii::app()->theme->name == 'demo') {
				$model->dateFrom =  Yii::app()->params['reports.date_now'];
				$model->dateTo = Yii::app()->params['reports.date_now'];
			}
			else {			
				$model->dateFrom = Yii::app()->DateFormatter->format('dd.MM.yyyy', time());
				$model->dateTo = Yii::app()->DateFormatter->format('dd.MM.yyyy', time());
			}
		}
		$view = 'index';
		if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
			$view = 'excel';
		$this->render('sales/'.$view,array('model'=>$model,	));
	}
	
	public function actionSumSales()
	{		
		$model = new RptSales('search');
		$model->unsetAttributes();
		if(isset($_GET['RptSales'])) {			
			$model->attributes=$_GET['RptSales'];
		};
		$groupDefs = array(
			'azs' => array(
					'alias' => 'azs',
					'field' => 'azs.name',
					'header' => 'АЗС',
			),
			'driver' => array(
					'alias' => 'driverName',
					'field' => 'card.owner',
					'header' => 'Водители',
			),
			'auto' => array(
					'alias' => 'autoName',
					'field' => 'autoCard.owner',
					'header' => 'Автомобили',
			),
			'terminal' => array(
					'alias' => 'terminalName',
					'field' => 'terminal.name',
					'header' => 'Терминалы',
			),			
		);
		if (Yii::app()->theme->name == 'cardowner-driver-auto') {
			$groupDefs['auto'] = array(
						'alias' => 'autoName',
						'field' => 'card.description',
						'header' => 'Автомобили',
			);
		}
		else if (Yii::app()->theme->name == 'clients') {
			$groupDefs['client'] = array(
						'alias' => 'organizationName',
						'field' => 'organization.name',
						'header' => 'Клиенты',
			);
		}
		else if (Yii::app()->theme->name == 'shifts') {
			$groupDefs['driver']['header'] = 'Карта-владелец';
			$groupDefs['auto'] = array(
						'alias' => 'autoName',
						'field' => 'card.description',
						'header' => 'Карта-описание',
			);
		}		
		
		$groups = array();
		for ($i = 1; $i <= 3; $i++) {
			if (!isset($_GET['group'.$i]))
				continue;
			$s = $_GET['group'.$i];
			if (isset($groupDefs[$s]) && (array_search($s, $groups) === false))
				$groups[] = $s;
		}		
		$criteria = new CDbCriteria();
		$s = '';
		foreach ($groups as $g) {
			$s = $s.$groupDefs[$g]['field'].' as '.$groupDefs[$g]['alias'].',';
		}
		$criteria->select = $s.'sum(`t`.volume) as volume, fuel.name as fuelName';
		$criteria->compare('operationType', 2);
		$criteria->compare('azsId', $model->azsId);
		$criteria->with = array(
				'card' => array(
						'select' => 'owner',
				),
				'autoCard' => array(
						'select' => 'owner',
				),
				'pumptransaction.terminal' => array(
						'select' => 'name',
				),
				'pumptransaction.terminal.azs' => array(
						'select' => 'name',
				),
				'fuel' => array(
						'select' => 'code, name',
				),
		);
		if (Yii::app()->theme->name == 'clients') {
			$criteria->with = array_merge($criteria->with, array(
				'card.organization' => array(
						'select' => 'name',
				),			
			));		
		}

		$s = '';
		foreach ($groups as $g) {
			$s = $s.','.$groupDefs[$g]['field'];
		}
		$s = $s.',`fuel`.`name`';
		$criteria->order = substr($s, 1);
		$criteria->group = substr($s, 1);

		$now = getdate(time());
		$dt = date_create();
		date_date_set($dt, $now['year'], $now['mon'], 1);
		if (!isset($model->dateFrom) || ($model->dateFrom == ''))
			$model->dateFrom = (Yii::app()->theme->name != 'demo') ? date_format($dt, 'd.m.Y') : Yii::app()->params['reports.date_from'];
		$criteria->compare('date',">=".MyUtils::datetimeFormat('y-M-d 0:0:0', $model->dateFrom));
		date_add($dt, new DateInterval('P1M'));
		date_sub($dt, new DateInterval('P1D'));
		if (!isset($model->dateTo) || ($model->dateTo == ''))
			$model->dateTo = (Yii::app()->theme->name != 'demo') ? date_format($dt, 'd.m.Y') : Yii::app()->params['reports.date_to'];
		$criteria->compare('date',"<=".MyUtils::datetimeFormat('y-M-d 23:59:59', $model->dateTo));

		if (isset($model->terminalId))
			$criteria->compare('terminal.id', $model->terminalId);
				
		$view = 'index';
		if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
			$view = 'excel';
		$this->render('sumsales/'.$view,array(
				'model'=>$model,	
				'criteria'=>$criteria,	
				'groupDefs'=>$groupDefs,	
				'groups'=>$groups,	
		));
	}

	public function actionFuelMove()
	{
		$model=new TankBookStates('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TankBookStates']))
			$model->attributes=$_GET['TankBookStates'];
		else {
			if (Yii::app()->theme->name == 'demo') {
				$model->dateFrom =  Yii::app()->params['reports.date_from'];
				$model->dateTo = Yii::app()->params['reports.date_to'];
			}
			else {			
				$now = getdate(time());
				$dt = date_create();
				date_date_set($dt, $now['year'], $now['mon'], 1);
				$model->dateFrom = date_format($dt, 'd.m.Y');
				date_add($dt, new DateInterval('P1M'));
				date_sub($dt, new DateInterval('P1D'));
				$model->dateTo = date_format($dt, 'd.m.Y');
			}
		}	
		$criteria = new CDbCriteria();
		if (isset($model->dateFrom) && ($model->dateFrom != '') ){
			$from = date("Y-m-d", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $model->dateFrom)));
			$criteria->compare('t.date',">= $from",true);
		}
		if (isset($model->dateTo) && ($model->dateTo != '') ){
			$to = date("Y-m-d", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $model->dateTo)));
			$criteria->compare('t.date',"<= $to",true);
		}
		$criteria->compare('azs.id', $model->azsId);
		$criteria->order = 't.date, tank.name';
		$criteria->with = array('tank', 'tankIncome', 'tank.azs');
		$view = 'index';
		if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
			$view = 'excel';
		$this->render('fuelmove/'.$view,array('model'=>$model, 'criteria'=>$criteria));		
	}	
	
	public function actionShift()
	{		
		if (isset($_GET['dateFrom']) && ($_GET['dateFrom'] != ''))
			$dateFrom = MyUtils::datetimeFormat('dd.MM.yyyy',$_GET['dateFrom']);
		else
			$dateFrom = (Yii::app()->theme->name != 'demo') ? MyUtils::datetimeFormat('dd.MM.yyyy') : Yii::app()->params['reports.date_now'];
		if (isset($_GET['dateTo']) && ($_GET['dateTo'] != ''))
			$dateTo = MyUtils::datetimeFormat('dd.MM.yyyy',$_GET['dateTo']);
		else
			$dateTo = (Yii::app()->theme->name != 'demo') ? MyUtils::datetimeFormat('dd.MM.yyyy') : Yii::app()->params['reports.date_now'];
		if (isset($_GET['azsId']))
			$azsId = $_GET['azsId'];
		else
			$azsId = 0;		
			
		$mysqlDateFrom = MyUtils::datetimeFormat('yyyy-MM-dd', $dateFrom);
		$mysqlDateTo = MyUtils::datetimeFormat('yyyy-MM-dd', $dateTo);
		$dt = new DateTime($mysqlDateFrom);		
		date_sub($dt, new DateInterval('P1D'));
		$mysqlDateFromSub1D = MyUtils::datetimeFormat('yyyy-MM-dd', $dt);
		
		$data = array();
		$tankIds = '0';
		foreach (Fuels::model()->findAll(array(
					'with' => array('tanks', 'tanks.azs'),
					'condition' => "azs.id = $azsId and tanks.visible > 0",
					'order' => 'code, tanks.name, tanks.id')
				) as $fuel) {
			if (count($fuel->tanks) == 0)
				continue;
			$data[$fuel->id] = array();
			$data[$fuel->id]['info'] = $fuel->name;
			$data[$fuel->id]['tanks'] = array();
			$tanks = &$data[$fuel->id]['tanks'];
			foreach ($fuel->tanks as $tank) {
				$tanks[$tank->id] = array();
				$t = &$tanks[$tank->id];
				$t['info'] = $tank->name;
				$t['beginRealVolume'] = 0;
				$t['beginBookVolume'] = 0;
				$t['incomeVolume'] = 0;
				$t['moveInVolume'] = 0;
				$t['saleVolume'] = 0;
				$t['moveOutVolume'] = 0;
				$t['serviceVolume'] = 0;
				$t['endRealVolume'] = 0;
				$t['endBookVolume'] = 0;				
				unset($t);
				$tankIds = $tankIds.', '.$tank->id;
			}
			unset($tanks);			
		}		
		
		$sql = 'call tankBookStateIdByDate(:in_date, :in_tankId, @out_stateId)';
		$command = Yii::app()->db->createCommand($sql);
		foreach (Tanks::model()->findAll(array('condition' => 'id in ('.$tankIds.')')) as $tank) {
			$command->bindValues(array('in_date'=>$mysqlDateFromSub1D, 'in_tankId'=>$tank->id));
			$command->execute();
			$command->bindValues(array('in_date'=>$mysqlDateTo, 'in_tankId'=>$tank->id));
			$command->execute();			
		}
				
		$criteria = new CDbCriteria;
		$criteria->condition = "t.date = '".$mysqlDateFromSub1D."'";
		 
		foreach (TankBookStates::model()->findAll($criteria) as $state) {
			if (isset($data[$state->fuelId]) && isset($data[$state->fuelId]['tanks'][$state->tankId]))
				$data[$state->fuelId]['tanks'][$state->tankId]['beginBookVolume'] = $state->fuelVolume;
		}
		
		$criteria->condition = "t.date = '".$mysqlDateTo."'";		
		foreach (TankBookStates::model()->findAll($criteria) as $state)		
			if (isset($data[$state->fuelId]) && isset($data[$state->fuelId]['tanks'][$state->tankId]))
				$data[$state->fuelId]['tanks'][$state->tankId]['endBookVolume'] = $state->fuelVolume;
		
		$criteria->select = 'fuelId, tankId,'
			.'sum(incomeVolume) as  incomeVolume'
			.', sum(moveInVolume) as moveInVolume'
			.', sum(moveOutVolume) as moveOutVolume'
			.', sum(saleVolume) as saleVolume'		
			.', sum(serviceVolume) as serviceVolume';		
		$criteria->group = 'fuelId, tankId';
		$criteria->condition = "date >= '".$mysqlDateFrom."' and date <= '".$mysqlDateTo."'";
		foreach (TankBookStates::model()->findAll($criteria) as $state)
			if (isset($data[$state->fuelId]) && isset($data[$state->fuelId]['tanks'][$state->tankId])) {
				 $v = &$data[$state->fuelId]['tanks'][$state->tankId];
				 $v['incomeVolume'] = $state->incomeVolume;
				 $v['moveInVolume'] = $state->moveInVolume;
				 $v['moveOutVolume'] = $state->moveOutVolume;
			 	 $v['saleVolume'] = $state->saleVolume;
			 	 $v['serviceVolume'] = $state->serviceVolume;
			 	unset($st);
			}
		
		foreach ($data as &$fuel) {
			foreach ($fuel['tanks'] as $tankId => &$tank) {
//				TankRealStates::model()->findAll();
				$criteria = new CDbCriteria();
				$criteria->order = 'date desc';
				$criteria->limit = 1;				
				$criteria->condition = "date < '".$mysqlDateFrom."' and tankId = ".$tankId;
				$rstates = TankRealStates::model()->findAll($criteria);
				if (count($rstates) > 0) 
					$tank['beginRealVolume'] = $rstates[0]->fuelVolume;
				else
					$tank['beginRealVolume'] = 0;

				$criteria = new CDbCriteria();
				$criteria->order = 'date desc';
				$criteria->limit = 1;				
				$criteria->condition = "cast(`date` as date) = '".$mysqlDateTo."' and tankId = ".$tankId;
				$rstates = TankRealStates::model()->findAll($criteria);
				if (count($rstates) > 0)
					$tank['endRealVolume'] = $rstates[0]->fuelVolume;
				else {
					$criteria = new CDbCriteria();
					$criteria->order = 'date asc';
					$criteria->limit = 1;				
					$criteria->condition = "cast(`date` as date) > '".$mysqlDateTo."' and tankId = ".$tankId;
					$rstates = TankRealStates::model()->findAll($criteria);
					if (count($rstates) > 0)
						$tank['endRealVolume'] = $rstates[0]->fuelVolume;
					else
						$tank['endRealVolume'] = '-'; 
				}
				
			}
			unset($tank);			
		}
		unset($fuel);
	
		$view = 'index';
		if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
			$view = 'excel';
		$this->render('shift/'.$view,array(
				'dateFrom'=>$dateFrom,
				'dateTo'=>$dateTo,
				'azsId' => $azsId,				
				'data'=>$data,
		));			
	}
	
	
	public function actionShiftN()
	{		
		$model=new Shifts('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Shifts'])) {
			$model ->attributes=$_GET['Shifts'];
		}
			
		$azs = new Azs('search'); 
		$azs->unsetAttributes();  // clear any default values
		if(isset($_GET['Azs'])) {	
			$azs->attributes=$_GET['Azs']; 
			$m = $azs->findByPk($azs->id);
			if (isset($m))
				$azs = $m;			
		}
		else {
			$azs = Azs::model()->find(array('order'=>'id'));
		}
	
		$crit = new CDbCriteria;
		$crit->compare('azsId', $azs->id);
		$shift = null;
		if (isset($model->number) && (is_numeric($model->number))) {
			$crit->compare('number', $model->number);
			$shift = Shifts::model()->find($crit);
		}
		else if (isset($model->id)) {
			$crit->compare('id', $model->id);
			$shift = Shifts::model()->find($crit);
		}
		Yii::log(print_r($shift, true));
		
		if (!isset($shift)) {
			$crit = new CDbCriteria;			
			$crit->compare('azsId', $azs->id);
			$crit->order = 't.id desc';
			$shift = Shifts::model()->find($crit);
		}
		
		$model = $shift;
		
		$crit = new CDbCriteria;
		$crit->compare('shiftId', $shift->id);
		$crit->with = array('tank', 'fuel');
		$crit->order = 'fuel.code, tank.number';
		$shifttanks = ShiftTanks::model()->findAll($crit);
		
		$view = 'index';
		if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
			$view = 'excel';
		$this->render('shift/'.$view,array(
				'model'=>$model,
				'azs'=>$azs,
				'shift'=>$shift,
				'shifttanks'=>$shifttanks,
		));			
	}
	
	public function actionTankRealStates() {
		$model=new TankRealStates('search');
		$model->unsetAttributes(); 
		if(isset($_GET['TankRealStates']))
			$model->attributes=$_GET['TankRealStates'];
		else {
			if (Yii::app()->theme->name == 'demo') {
				$model->dateFrom =  Yii::app()->params['reports.date_from'];
				$model->dateTo = Yii::app()->params['reports.date_to'];
			}
			else {						
				$dt = date_create();
				$model->dateFrom = date_format($dt, 'd.m.Y');
				$model->dateTo = $model->dateFrom;
			}
		}
		$model->with('tank', 'tankIncome', 'tankMoveFrom', 'tankMoveTo', 'tank.azs');
		$view = 'index';
		if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
			$view = 'excel';
		$this->render('tankRealStates/'.$view,array('model'=>$model));	
	}
	
    public function actionTerminals() {
                $model = new Azs('search');
                $model->unsetAttributes();
                if(isset($_GET['Azs'])) {
                        $model->attributes=$_GET['Azs'];
                }
                $criteria = new CDbCriteria();
                $criteria->with = array('terminals',);
                $criteria->order = 't.name, terminals.name';

                $view = 'index';
                if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
                        $view = 'excel';
                $this->render('terminals/'.$view,array('model'=>$model, 'criteria' => $criteria));
    }
	
	public function actionCounters() {
		$model = new Azs('search');
		$model->unsetAttributes();
		if(isset($_GET['Azs'])) {
			$model->attributes=$_GET['Azs'];
		}
		$criteria = new CDbCriteria();
		$criteria->with = array('terminals', 'terminals.pumps', 'terminals.pumps.tank', 'terminals.pumps.tank.fuel');
		$criteria->order = 't.name, terminals.name, pumps.pumpNo, pumps.nozzleNo';
		
		$view = 'index';
		if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
			$view = 'excel';
		$this->render('counters/'.$view,array('model'=>$model, 'criteria' => $criteria));
	}
	
	public function actionWialon()
	{		
		Yii::import('application.services.reports.wialon.ReportData');
		$model = new ReportData();
		
		if(isset($_GET['ReportData'])) {			
			$model->attributes=$_GET['ReportData'];
		};	

		$view = 'index';
		if (isset($_GET['view']) && ($_GET['view'] == 'excel'))
			$view = 'excel';
		
		$this->render('wialon/'.$view,array(
				'model'=>$model,	
		));		
	}	
	
}
