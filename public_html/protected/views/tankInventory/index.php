<?php $this->pageTitle = 'Инвентаризация'; ?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-income-create',
							'buttonType'=>'button',
							'caption'=>'Инвентаризация',
							'htmlOptions'=>array('class'=>'btn-add'),
							'url'=>Yii::app()->createUrl("tankInventory/create"),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("tankInventory/create").'";return false;}',
						)
				 );
  		?>
	</div>
</div>

<div class="toolbar grid-toolbar">
	<?php $this->widget('zii.widgets.jui.CJuiButton',
			array(
					'name'=>'btn-xls',
					'buttonType'=>'button',
					'htmlOptions'=>array('class'=>'tb-btn xls-tb-btn', 'title'=>'excel'),
					'onclick'=>'js:function(){gridtoexcel("tankInventory-grid","'.$this->createUrl('excel').'");}',
			)
	);
	?>
</div>

<?php


 function columnDate($data) {
 	$s =  Yii::app()->DateFormatter->formatDateTime($data->date,"medium",null);
 	return	'<a href="'.Yii::app()->createUrl("tankInventory/update",array("id"=>$data->id)).'" title="Изменить">'.$s.'</a>';
 }

$arr = date_parse(date(DATE_RSS));
$year = $arr['year'];
 
$this->widget('application.extensions.MyGridView', array(
	'id'=>'tankInventory-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'class'=>'application.extensions.SYDateColumn',
			'name'=>'date',
			'value'=>'columnDate($data)',
			'fromText'=>'c',
			'toText'=>'по',
			'dateLabelStyle'=>'width:20%;display:block;float:left;margin-bottom: 3px;',//width:25%;display:block;float:left;',
			'dateInputStyle'=>'width:70%',			
			'dateFormat'=>'dd.mm.yy',
			'language'=>'ru',
			'dateOptions'=>array(
				'showAnim'=>'fold',
				'dateFormat'=>'dd.mm.yy',
				'changeYear'=> true,
				'showButtonPanel' => true,
				'yearRange' => ($year - 2).':'.$year,
			),
			'headerHtmlOptions' => array('width'=>'100', ),
			'type' =>'raw',				
		),		
		array(
			'name'=>'tankId',
			'value'=>'$data->tank->fullName("azs,name")',
			'filter'=>Tanks::listData('azs'),
		),			
		array(
			'name'=>'fuelId',
			'value'=>'$data->fuel->name',
			'filter'=>CHtml::listData(Fuels::model()->findall(array('order' => 'name',)), 'id', 'name'),				
		),
		array(
			'name' => 'oldVolume',
			'header' => 'Остаток,л',
			'value'=>'sprintf("%3.0f", $data->oldVolume)',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'operVolume',
			'header' => 'По факту|Топливо,л',				
			'value'=>'sprintf("%3.0f", $data->operVolume)',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'restVolume',
			'header' => 'По факту|Мертвый ост.,л',				
			'value'=>'sprintf("%3.0f", $data->operRest)',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'volumeDiff',
			'header' => 'Разница,л',				
			'value'=>'sprintf("%3.0f", $data->volumeDiff)',
			'filter' => false,
			'cssClassExpression'=>'$data->volumeDiff < 0 ? "number-cell error-cell": "number-cell"',
		),
			
	),
));
?>
