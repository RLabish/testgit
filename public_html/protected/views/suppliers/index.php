<?php $this->pageTitle = 'Поставщики'; ?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-auto-create',
							'buttonType'=>'button',
							'caption'=>'Новый поставщик',
							'htmlOptions'=>array('class'=>'btn-add'),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("suppliers/create").'";return false;}',
						)
				 );
  		?>
	</div>
</div>

<?php

 function columnName($data) {
 	return	'<a href="'.Yii::app()->createUrl("suppliers/update",array("id"=>$data->id)).'" title="Изменить">'.$data->name.'</a>';
 }
 
$this->widget('application.extensions.MyGridView', array(
	'id'=>'suppliers-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'name',
			'value'=>'columnName($data)',
			'type' =>'raw',				
		),
		'description',			
	),
	'summaryCssClass' => 'grid-view-toolbar',
	'summaryText'=>
		'{start}-{end} из {count}' /* .
		CHtml::link('xls', array('excel',))*/,
));
?>
