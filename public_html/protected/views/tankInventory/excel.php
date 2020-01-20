<h1>Excel</h1>

<?php 
Yii::import('application.extensions.XLReport');

$xl = new XLReport();
$xl->make('tankinv', 'data', $model->search(), array(
		array(
			'name' => 'date',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yyyy\',$data->date)',				
		), 
		array(
			'name' => 'doc',
			'value'=>'$data->doc',
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
			'name' => 'fuel',
			'value' => '$data->fuel->name',
		),
		array (
			'name' => 'oldVolume',
			'value' => '$data->oldVolume',
			'format' => 'numeric',
		),
		array (
				'name' => 'operVolume',
				'value' => '$data->operVolume',
				'format' => 'numeric',
		),
		array (
				'name' => 'restVolume',
				'value' => '$data->operRest',
				'format' => 'numeric',
		),
		array (
				'name' => 'volumeDiff',
				'value' => '$data->volumeDiff',
				'format' => 'numeric',
		),		
));
?>
