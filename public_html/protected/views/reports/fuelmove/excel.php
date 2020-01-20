<?php 
Yii::import('application.extensions.XLReport');

$provider = new CActiveDataProvider($model, array('criteria'=>$criteria, 'pagination' => false,));
$data = $provider->getData();

$xl = new XLReport();
$xl->start('fuelmove');

$rowNo = 0;
foreach ($data as $row) {
	$tankMove = array();
	if ($row->tankMoveFrom() != null) $tankMove = array_merge($tankMove, $row->tankMoveFrom());
	if ($row->tankMoveTo() != null) $tankMove = array_merge($tankMove, $row->tankMoveTo());

	$subRowCount = max(count($row->tankIncome), count($tankMove), 1);	
	$band = array();
	$band['date'] = Yii::app()->DateFormatter->formatDateTime($row->date,'medium',null);
	$band['azs'] = $row->tank->azs->name;
	$band['tank'] = $row->tank->name;
	$band['fuel'] = $row->tank->fuel->name;
	$band['saleVolume'] = array (
			'format' => 'numeric',
			'value' => $row->saleVolume,
	);
	$band['serviceVolume'] = array (
			'format' => 'numeric',
			'value' => $row->serviceVolume,
	);
	$band['fuelVolume'] = array (
			'format' => 'numeric',
			'value' => $row->fuelVolume,
	);
	
	if (count($row->tankIncome) > 0) {
		$band['supplier'] = $row->tankIncome[0]->supplier->name;
		$band['incomeVolume'] = array (
				'format' => 'numeric',
				'value' => $row->tankIncome[0]->volume,
		);
	}


		
	if (count($tankMove) > 0) {
		if (isset($tankMove[0]->tankTo) && $tankMove[0]->tankTo->id == $row->tankId) {
			$band['tankMoveName'] = $tankMove[0]->tankFrom->azs->name;
			$band['tankMoveVolume'] = array (
				'format' => 'numeric',
				'value' => $tankMove[0]->volume,
			);			
		}
		else {
			if (isset($tankMove[0]->tankTo))
				$band['tankMoveName'] = $tankMove[0]->tankTo->azs->name;
			$band['tankMoveVolume'] = array (
					'format' => 'numeric',
					'value' => -$tankMove[0]->volume,
				);
		}
		
	}
	$xl->add('data', $band);
	
	$band = array();
	
	for ($i = 1; $i < $subRowCount; $i++) {
		if ($i < count($row->tankIncome)) {
			$band['supplier'] = $row->tankIncome[$i]->supplier->name;
			$band['incomeVolume'] = array (
					'format' => 'numeric',
					'value' => $row->tankIncome[$i]->volume,
			);			
		}
		if ($i < count($tankMove)) {
			if (isset($tankMove[$i]->tankTo) && ($tankMove[$i]->tankTo->id == $row->tankId)) {
				$band['tankMoveName'] = $tankMove[$i]->tankFrom->azs->name;
				$band['tankMoveVolume'] = array (
					'format' => 'numeric',
					'value' => $tankMove[$i]->volume,
				);				
			}
			else {
				if (isset($tankMove[$i]->tankTo)) 
					$band['tankMoveName'] = $tankMove[$i]->tankTo->azs->name;
				$band['tankMoveVolume'] = array (
					'format' => 'numeric',
					'value' => -$tankMove[$i]->volume,
				);				
			}			
		}
		$xl->add('data', $band);
		$band = array();
	}
	
}
$xl->complete();
?>
