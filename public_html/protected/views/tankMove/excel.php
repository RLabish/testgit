<h1>Excel</h1>

<?php 
Yii::import('application.extensions.XLReport');

$xl = new XLReport();
$xl->make('tankmove', 'data', $model->search(), array(
		array(
			'name' => 'date',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yyyy\',$data->date)',				
		), 
		array (
			'name' => 'tankFrom',
			'value'=>'$data->tankFrom->fullName("azs,name")',
		),
		array (
			'name' => 'tankTo',
			'value'=>'isset($data->tankTo)?$data->tankTo->fullName("azs,name"):""',
		),
		array (
			'name' => 'doc',
			'value'=>'$data->doc',
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
