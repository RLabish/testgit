<?php 
Yii::import('application.extensions.XLReport');

$xl = new XLReport();
$xl->make('tankrealstates', 'data', $model->search(), array(
		array(
			'name' => 'date',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yy HH:mm\',$data->date)',				
		), 
		array (
			'name' => 'fuelLevel',
			'format' => 'numeric',
		),
		array (
			'name' => 'fuelVolume',
			'format' => 'numeric',
		),
		array (
			'name' => 'fuelMass',
			'format' => 'numeric',
		),
		array (
			'name' => 'temperature',
			'format' => 'numeric',
		),
		array (
			'name' => 'density',
			'format' => 'numeric',
		),
		array (
			'name' => 'waterLevel',
			'format' => 'numeric',
		),
		array (
			'name' => 'waterVolume',
			'format' => 'numeric',
		),		
));

?>
