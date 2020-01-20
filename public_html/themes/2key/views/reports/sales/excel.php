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
		array(
			'name' => 'driverCard',
			'value' => '(isset($data->card))? $data->card->number:""',
		),
		array(
			'name' => 'driverName',
			'value' => '(isset($data->card))? $data->card->owner:""',
		),
		array(
			'name' => 'autoCard',
			'value' => '(isset($data->autoCard))?$data->autoCard->number:""',
		),
		array(
			'name' => 'autoName',
			'value' => '(isset($data->autoCard))?$data->autoCard->owner:""',
		),
		array (
			'name' => 'fuel',
			'value' => '$data->fuel->name',
		),
		array (
			'name' => 'volume',
			'format' => 'numeric',
		),
));

?>
