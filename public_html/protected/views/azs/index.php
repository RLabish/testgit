<?php $this->pageTitle = 'АЗС'; ?>


<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php 
		if (Yii::app()->user->checkAccess('admin')) {
			$this->widget('zii.widgets.jui.CJuiButton',						
						array(
							'name'=>'btn-client-create',
							'buttonType'=>'button',
							'caption'=>'Новая АЗС',
							'htmlOptions'=>array('class'=>'btn-add'),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("azs/create").'";return false;}',
						)
				 );
		} 
  		?>
	</div>
</div>


<?php


  function columnName($data) {
	static $f = null;
	if ($f == null)
		$f = Yii::app()->user->checkAccess('admin');
	if ($f)
		return	'<a href="'.Yii::app()->createUrl("azs/update",array("id"=>$data->id)).'" title="Изменить">'.$data->name.'</a>';
	else
		return $data->name;
 }
 
$this->widget('application.extensions.MyGridView', array(
	'id'=>'clients-grid',
	'dataProvider'=>$model->search(),
//	'filter'=>$model,
	'columns'=>array_merge( array(
		array(
			'name'=>'name',
			'value'=>'columnName($data)',
			'type' =>'raw',				
		),
		'note',	
		)
	),
	'summaryCssClass' => 'grid-view-toolbar',
	'summaryText'=>
		'{start}-{end} из {count}' /* .
		CHtml::link('xls', array('excel',))*/,
));
?>
