<?php 
$this->pageTitle = 'Заправочная ведомость';
$filter = 'sales/filter'; 
   
if (!isset($_GET['submit'])) {
?>
	<h1><?php echo $this->pageTitle; ?></h1>
	<?php
	$this->renderPartial($filter, array('model'=>$model));
	return;	
}
?>

<?php $this->renderPartial($filter, array('model'=>$model));  ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>

 <div class="toolbar grid-toolbar">
	<?php $this->widget('zii.widgets.jui.CJuiButton',
				array(
						'name'=>'btn-xls',
						'buttonType'=>'button',
						'htmlOptions'=>array('class'=>'tb-btn xls-tb-btn', 'title'=>'excel'),						
						'onclick'=>
						'js:function(){'
						.'location.href = \''.Yii::app()->request->requestUri.'&view=excel'.'\';'
						.'return false;'
						.'}'
				)
		);
	?>
</div>

 <?php
 $criteria=new CDbCriteria;
 $criteria->order = 'code';

 $totalvolumes = array();
 foreach (Fuels::model()->findAll($criteria) as $fuel) {
	$totalvolumes[$fuel->name] = $model->getFuelTotals($fuel->id);
 }

 function fuelNameCells($totalvolumes) {
	$s = '<table class="tbl-in-cell"><tbody>';
    foreach ($totalvolumes as $k => $x)
		$s = $s.'<tr><td>'.$k.'</td><tr>';
 	$s = $s.'</tbody></table>';
	return $s;
 }

 function fuelVolumeCells($totalvolumes) {
	$s = '<table class="tbl-in-cell"><tbody>';
    foreach ($totalvolumes as $k => $x)
		$s = $s.'<tr><td class="cell-number">'.number_format($x, 2, ".", " ").'</td><tr>';
 	$s = $s.'</tbody></table>';
	return $s;
 }
 
 $provider = $model->search();
 //$provider->pagination = false;
 $this->widget('application.extensions.MyGridView', array(
	'id'=>'sales-grid',
	'dataProvider'=> $provider,
 	'template'=>"{items}", 	
	'columns'=>array(
			array(
					'header' => 'Дата',
					'name' => 'date',
					'value' => 'Yii::app()->DateFormatter->format("dd.MM.yy HH:mm",$data->date)',
			),
			array(
					'header' => 'АЗС',
					'name' => 'pumptransaction.terminal.azs.name',
			),
			array(
					'header' => 'Водитель',
					'name' => 'card.owner',
			),
			array(
					'header' => 'Продукт',
					'name' => 'fuel.name',
					'footer'=>fuelNameCells($totalvolumes),
					'footerHtmlOptions'=>array('class'=>'multi-cell'),										
			),
			array(
					'header' => 'Объем, л',
					'name' => 'volume',
					'value' =>'number_format($data->volume, 2, ".", " ")',
					'htmlOptions' => array ('class' => 'cell-number',),
					'footer'=>fuelVolumeCells($totalvolumes),
					'footerHtmlOptions'=>array('class'=>'multi-cell'),							
			),
	),
)); 
?>
 
 <?php
	$baseUrl = Yii::app()->baseUrl;
	$cs = Yii::app()->getClientScript();
	$cs->registerCssFile($baseUrl.'/css/report.css');
?>