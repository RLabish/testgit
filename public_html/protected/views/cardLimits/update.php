<?php 
$card = Cards::model()->find(array('condition'=>'id=:id','params'=>array(':id'=>$model->cardId)));
$this->pageTitle = 'Лимит по карте'.$card->number.' /'.$card->owner.'/';
?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
	<div class="toolbar">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-lim-del',
							'htmlOptions'=>array('class'=>'tb-btn del-tb-btn', 'title'=>'Удалить'),
							'onclick'=>	'js:function(){'
								.'	if(confirm("Удалить лимит?")) {'
								.'location.href = \''.Yii::app()->createUrl("cardLimits/delete", array('id'=>$model->id)).'\';'
								.'		return false;'
								.'	} else return false;'
								.'}'																
						)
				 );
  		?>
	</div>
</div>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
