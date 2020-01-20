<?php $this->pageTitle = 'Перемещение топлива'; ?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-move-create',
							'buttonType'=>'button',
							'caption'=>'Перемещение',
							'htmlOptions'=>array('class'=>'btn-add'),
							'url'=>Yii::app()->createUrl("tankMove/create"),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("tankMove/create").'";return false;}',
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
					'onclick'=>'js:function(){gridtoexcel("tankmove-grid","'.$this->createUrl('excel').'");}',
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
 	return	'<a href="'.Yii::app()->createUrl("tankMove/update",array("id"=>$data->id)).'" title="Изменить">'.$s.'</a>';
 }

 function columnTankFrom($data) {
	if (isset($data->tankFrom))
		return $data->tankFrom->fullName("azs,name");
	else
		return '';
 }
 
 function columnTankTo($data) {
	if (isset($data->tankTo))
		return $data->tankTo->fullName("azs,name");
	else
		return '';
 }
 
$arr = date_parse(date(DATE_RSS));
$year = $arr['year'];
 
$this->widget('application.extensions.MyGridView', array(
	'id'=>'tankmove-grid',
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
			'name'=>'tankFromId',
			'value'=>'columnTankFrom($data)',
			'filter'=>Tanks::listData('azs'),
		),		
		array(
			'name'=>'tankToId',
			'value'=>'columnTankTo($data)',
			'filter'=>Tanks::listData('azs'),
		),		
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
