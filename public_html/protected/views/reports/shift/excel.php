<?php 
Yii::import('application.extensions.XLReport');

$xl = new XLReport();
$xl->start('shift');

foreach ($data as $fuel) 
	foreach ($fuel['tanks'] as $tank) 
		$xl->add('data', array(
			'fuel' => array(
				'value' => $fuel["info"],
			),
			'tank' => array(
				'value' => $tank["info"],
			),
			'beginRealVolume' => array (
				'format' => 'numeric',
				'value' => $tank["beginRealVolume"],
			),
			'beginBookVolume' => array (
				'format' => 'numeric',
				'value' => $tank["beginBookVolume"],
			),
			'incomeVolume' => array (
				'format' => 'numeric',
				'value' => $tank["incomeVolume"],
			),
			'moveInVolume' => array (
				'format' => 'numeric',
				'value' => $tank["moveInVolume"],
			),
			'saleVolume' => array (
				'format' => 'numeric',
				'value' => $tank["saleVolume"],
			),					
			'moveOutVolume' => array (
				'format' => 'numeric',
				'value' => $tank["moveOutVolume"],
			),
			'serviceVolume' => array (
				'format' => 'numeric',
				'value' => $tank["serviceVolume"],
			),		
			'endRealVolume' => array (
				'format' => 'numeric',
				'value' => $tank["endRealVolume"],
			),
			'endBookVolume' => array (
				'format' => 'numeric',
				'value' => $tank["endBookVolume"],
			),
			'diffRealBookVolume' => array (
				'format' => 'numeric',
				'value' => $tank["endRealVolume"] - $tank["endBookVolume"],
			),
		));
$xl->complete();
?>
