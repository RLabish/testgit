<?php

class ReportData extends Cards
{
	public $dateFrom;
	public $dateTo;
	public $autoname;	
	public $fuelname;	
	public $volume;
	
	public $motoH;
	public $motoKM;
	public $dut1;
	public $dut2;
	
	

	public function rules()
	{
		return array(
				array('dateFrom, dateTo', 'safe'),
		);
	}
	
	protected function afterConstruct() {
		$dt = date_create();
		$this->dateFrom = date_format($dt, 'd.m.Y 00:00:00');
		$this->dateTo = date_format($dt, 'd.m.Y 23:59:59');		
		return parent::afterConstruct();	
		/*
		$now = getdate(time());
		$dt = date_create();
		date_date_set($dt, $now['year'], $now['mon'], 1);
		$this->dateFrom = date_format($dt, 'd.m.Y 00:00:00');
		date_add($dt, new DateInterval('P1M'));
		date_sub($dt, new DateInterval('P1D'));
		$this->dateTo = date_format($dt, 'd.m.Y 23:59:59');		
		return parent::afterConstruct();	
		*/
	}	
	
	protected function afterFind()
	{
		parent::afterFind();
		
//		echo '$this->dateFrom:['.$this->dateFrom.']<br>';
		
//		echo DateTime::createFromFormat('d.m.Y H:i:s',$this->dateFrom)->format('d.m.Y H:i:s').'<br>';
		
		
//		return;
		
		$r = Yii::app()->wialonApi->report($this->gpsId, DateTime::createFromFormat('d.m.Y H:i:s',$this->dateFrom), DateTime::createFromFormat('d.m.Y H:i:s',$this->dateTo));
		if (isset($r['reportResult'])) {
			foreach ($r['reportResult']['stats'] as $x) {
				if ($x[0] == 'Моточасы') $this->motoH = $x[1];
				else if ($x[0] == 'Пробег в моточасах') $this->motoKM = explode( ' ', $x[1])[0];
				else if ($x[0] == 'Нач. уровень') $this->dut1 = explode( ' ', $x[1])[0];
				else if ($x[0] == 'Конеч. уровень') $this->dut2 = explode( ' ', $x[1])[0];
			}
		}
			/*
			
       Array ( [0] => Array ( [0] => Потрачено по расчету [1] => 0 л ) [1] => Array ( [0] => Нач. уровень [1] => 23.07 л ) 
	           [2] => Array ( [0] => Конеч. уровень [1] => 18.40 л ) [3] => Array ( [0] => Показания датчиков счетчиков [1] => 0 ) 
			   [4] => Array ( [0] => Моточасы [1] => 110:46:50 ) [5] => Array ( [0] => Пробег в моточасах [1] => 2340 км )
			   [6] => Array ( [0] => Длительность простоя [1] => 0:00:00 ) ) 

			
		}
		*/
		
		
		/*
		Yii::app()->wialonApi->report();

		чсм
		ыва
		ыва
		
		
		$this->expireFmt = Yii::app()->DateFormatter->formatDateTime($this->expire,'medium',null);
		$this->ok = false;
		switch ($this->state) {
			case 0:
				$now = Yii::app()->DateFormatter->format('yyyy-MM-dd', time());
				if ($this->expire < $now)
					$this->stateAsText = 'просрочена';
				else {
					$this->ok = true;
					$this->stateAsText = 'активна';
				}
				break;
			case 1:
				$this->stateAsText = 'заблокирована';
				break;
			default:
				$this->stateAsText = 'неизвестное состояние '.$this->model->state;								
		}
		if (isset($this->organization)) 
			$this->organizationName = $this->organization->name;	
*/		
	}


	
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->select = 't.id, t.gpsId, t.owner, t.owner as autoname, f.name as fuelname, sum(-op.volume) as volume'
		.", '".$this->dateFrom."' as dateFrom" 
		.", '".$this->dateTo."' as dateTo" 
		;
		$s = ' and (1=1)';
		if (isset($this->dateFrom) && ($this->dateFrom != '')) $s = $s.' and op.date >= "'.MyUtils::datetimeFormat('y-M-d H:m:s', $this->dateFrom).'"';
		if (isset($this->dateTo) && ($this->dateTo != '')) $s = $s.' and op.date <= "'.MyUtils::datetimeFormat('y-M-d H:m:s', $this->dateTo).'"';
		$criteria->compare('t.gpsId','>0');
		$criteria->join = 'left join cardoperations op on op.cardId = t.id'.$s
						  .' left join fuels f on f.id = op.fuelid';
		$criteria->group = 't.owner, f.name';	
		
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	
	
}