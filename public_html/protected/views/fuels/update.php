<?php $this->pageTitle = 'Вид топлива "'.$model->name.'"'; ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
	<div class="toolbar">
		<?php		
		$this->widget('zii.widgets.jui.CJuiButton',
						array(						
							'name'=>'btn-organization-del',
							'htmlOptions'=>array('class'=>'tb-btn del-tb-btn', 'title'=>'Удалить'),
							'onclick'=>
								'js:function(){'
								.'	if(confirm("Удалить вид топлива?")) {'
								.'location.href = \''.Yii::app()->createUrl("fuels/delete", array('id'=>$model->id)).'\';'
								.'		return false;'
								.'	} else return false;'
								.'}'								
								)
		);
  		?>
	</div>
</div>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
