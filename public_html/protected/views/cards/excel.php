<h1>Excel</h1>

<?php 
Yii::import('application.extensions.XLReport');

$xl = new XLReport();
$xl->make('cards', 'data', $model->search(), array(
		'number',
		'owner',
		'description',
		array(
			'name' => 'expire',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yyyy\',$data->expire)',				
		), 		
));

?>
