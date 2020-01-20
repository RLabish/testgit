<?php 
Yii::import('application.extensions.XLReport');

$xl = new XLReport();
$xl->make('sales', 'data', $model->search(), array(
		array(
			'name' => 'date',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yy HH:mm\',$data->date)',				
		), 
		array (
			'name' => 'azs',
			'value' => '$data->pumptransaction->terminal->azs->name',
		),
		'driverName',
		array (
			'name' => 'fuel',
			'value' => '$data->fuel->name',
		),
//		'volume',
		array (
			'name' => 'volume',
			'format' => 'numeric',
		),
));

?>
