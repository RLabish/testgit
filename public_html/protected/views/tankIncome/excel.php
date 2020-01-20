<h1>Excel</h1>

<?php 
Yii::import('application.extensions.XLReport');


$xl = new XLReport();
$xl->make('tankincome', 'data', $model->search(), array(
		array(
			'name' => 'date',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yyyy\',$data->date)',				
		), 
		array(
			'name' => 'azs',
			'value'=>'$data->tank->azs->name',
		),
		array (
			'name' => 'tank',
			'value' => '$data->tank->name',
		),
		array (
			'name' => 'supplier',
			'value' => '$data->supplierName',
		),
		array (
			'name' => 'doc',
			'value' => '$data->doc',
		),
		array (
			'name' => 'fuel',
			'value' => '$data->fuel->name',
		),
		array (
			'name' => 'volume',
			'value' => '$data->volume',
			'format' => 'numeric',
		),
));

?>
