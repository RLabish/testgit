<?php 
$this->pageTitle = 'Отчет по автомобилям';
$filter = 'wialon/filter'; 
   
if (!isset($_GET['submit'])) {
?>
	<h1><?php echo $this->pageTitle; ?></h1>
	<?php
	$this->renderPartial($filter, array('model'=>$model,));
	return;	
}
?>

<?php $this->renderPartial($filter, array('model'=>$model,));  ?>

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
 $this->widget('application.extensions.MyGridView', array(
	'id'=>'wialon-grid',
	'dataProvider'=> $model->search(),
 	'template'=>"{items}", 	
	'columns'=>array(
			array(
					'header' => 'Автомобиль',
					'name' => 'autoname',
			),			
			array(
					'header' => 'Заправлено|Продукт',
					'name' => 'fuelname',
			),
			array(
					'header' => 'Заправлено|Объем,л',
					'name' => 'volume',
					'value' =>'number_format($data->volume, 2, ".", " ")',
					'htmlOptions' => array ('class' => 'cell-number',),
			),
			array(
					'header' => 'Моточасы',
					'name' => 'motoH',
//					'value' =>'number_format($data->motoH, 2, ".", " ")',
					'htmlOptions' => array ('class' => 'cell-number',),
			),
			array(
					'header' => 'Пробег, км',
					'name' => 'motoKM',
//					'value' =>'number_format($data->motoKM, 2, ".", " ")',
					'htmlOptions' => array ('class' => 'cell-number',),
			),
			array(
					'header' => 'ДУТ|на начало, л',
					'name' => 'dut1',
//					'value' =>'number_format($data->dut1, 2, ".", " ")',
					'htmlOptions' => array ('class' => 'cell-number',),
			),
			array(
					'header' => 'ДУТ|на конец, л',
					'name' => 'dut2',
//					'value' =>'number_format($data->dut1, 2, ".", " ")',
					'htmlOptions' => array ('class' => 'cell-number',),
			),
	),
)); ?>
 
 <?php
	$baseUrl = Yii::app()->baseUrl;
	$cs = Yii::app()->getClientScript();
	$cs->registerCssFile($baseUrl.'/css/report.css');	
?>

