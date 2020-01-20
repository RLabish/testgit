<?php 
Yii::import('application.extensions.XLReport');


$xl = new XLReport();

$vars = array();
$i = 1;
foreach ($groups as $g) 
	$vars['group'.$i++.'_name'] = $groupDefs[$g]['header'];
foreach (Fuels::model()->findAll(array('order' => 'name')) as $f )
	$vars['fuel'.$f->code.'_name'] = $f->name;
	
$xl->start('sumsales'.count($groups), $vars);
$band = array();
$changed = false;

foreach ($model->findAll($criteria) as $row ) {
	if (count($band)  == 0)
		$changed = true;
	else {
		$i = 1;
		foreach ($groups as $g) {
			$name = $groupDefs[$g]['alias'];
			if ($band['group'.$i++] != $row->$name) {
				$changed = true;
				break;
			}
		}
	}
	if ($changed) {
		if (count($band) > 0) 
			$xl->add('data', $band);
		$band = array();
		$i = 1;
		foreach ($groups as $g) {
			$name = $groupDefs[$g]['alias'];
			$band['group'.$i++] = $row->$name;
		}
	}
	$band['fuel'.$row->fuel->code] = array (
		'format' => 'numeric',
		'value' => $row->volume,
	);
}
if (count($band) > 0) 
	$xl->add('data', $band);
$xl->complete();


/*





$xl = new XLReport();
$xl->make('sumsales', 'data', $model->search(), array(
		array(
			'name' => 'date',
			'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yy HH:mm\',$data->date)',				
		), 
		array (
			'name' => 'azs',
			'value' => '$data->pumptransaction->terminal->name',
		),
		'driverName',
		'autoName',
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
*/
?>
