<?php $this->pageTitle = 'Прием топлива #'.$model->id; ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
	<div class="toolbar">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-card-del',
//						 	'caption'=>'Удалить',
							'htmlOptions'=>array('class'=>'tb-btn del-tb-btn', 'title'=>'Удалить'),
							'onclick'=>
							//'js:function(){alert("Save button clicked"); this.blur(); return false;}',
								'js:function(){'
								.'	if(confirm("Удалить прием топлива?")) {'
								.'location.href = \''.Yii::app()->createUrl("tankIncome/delete", array('id'=>$model->id)).'\';'
								.'		return false;'
								.'	} else return false;'
								.'}'
								
								)
				 );
  		?>
	</div>
</div>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
