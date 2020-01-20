<h1>Excel</h1>

<?php 
Yii::import('application.extensions.XLReport');

$xl = new XLReport();
$xl->make('terminalevents', 'data', $model->search(), array(
		array(
			'name' => 'date',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yy HH:mm\',$data->date)',				
		), 
		array(
			'name' => 'terminal',
			'value'=>'$data->terminal->name',				
		),
		'msg',
));

?>
