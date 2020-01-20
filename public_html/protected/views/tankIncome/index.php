<?php 
  $this->pageTitle = 'Прием топлива';
?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-income-create',
							'buttonType'=>'button',
							'caption'=>'Прием топлива',
							'htmlOptions'=>array('class'=>'btn-add'),
							'url'=>Yii::app()->createUrl("tankIncome/create"),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("tankIncome/create").'";return false;}',
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
					'onclick'=>'js:function(){gridtoexcel("tankincome-grid","'.$this->createUrl('excel').'");}',
			)
	);
	?>
</div>

<?php
 function columnDate($data) {
	if ($data->tableSchema->columns['date']->dbType == 'date')
		$s =  MyUtils::datetimeFormat('dd.MM.yyyy', $data->date);	
	else
		$s =  MyUtils::datetimeFormat('dd.MM.yyyy HH:mm', $data->date);	
 	return	'<a href="'.Yii::app()->createUrl("tankIncome/update",array("id"=>$data->id)).'" title="Изменить">'.$s.'</a>';
 }

$arr = date_parse(date(DATE_RSS));
$year = $arr['year'];
 
$this->widget('application.extensions.MyGridView', array(
	'id'=>'tankincome-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
//	'actions'=>array('excel'),
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
			'name'=>'azsId',
			'value'=>'$data->tank->azs->name',
			'filter'=>CHtml::listData(Azs::model()->findall(array('order' => 'name',)), 'id', 'name'),
		),					
		array(
			'name'=>'tankId',
			'header'=>'Р-р',
			'value'=>'$data->tank->number',
			'filter'=> false,//CHtml::listData(Tanks::model()->findall(array('order' => 'name',)), 'id', 'name'),
		),			
		'supplierName',
		'doc',
		array(
			'name'=>'fuelId',
			'value'=>'$data->fuel->name',
			'filter'=>CHtml::listData(Fuels::model()->findall(array('order' => 'name',)), 'id', 'name'),				
		),
		array(
			'name' => 'volume',
			'value'=>'sprintf("%3.0f", $data->volume)',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
	),
));
?>
