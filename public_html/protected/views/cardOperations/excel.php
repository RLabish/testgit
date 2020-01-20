<h1>Excel</h1>

<?php 
Yii::import('application.extensions.XLReport');

$xl = new XLReport();
$xl->make('operations', 'data', $model->search(), array(
		array(
			'name' => 'date',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yy HH:mm\',$data->date)',				
		), 
		array(
			'name' => 'card',
			'value'=>'$data->card->number',				
		),
		array(
			'name' => 'driver',
			'value'=>'$data->card->owner',				
		),
		array(
			'name' => 'oper',
			'value'=>'$data->description',
		),
		array (
			'name' => 'fuel',
			'value' => '$data->fuel->name',
		),
		array (
			'name' => 'volume',
			'value' => '($data->operationType == 1)?$data->volume:-$data->volume',
			'format' => 'numeric',
		),
		array (
			'name' => 'balance',
			'value' => '$data->balance',
			'format' => 'numeric',
		),		
));

?>
