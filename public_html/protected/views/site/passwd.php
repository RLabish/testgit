<?php $this->pageTitle = 'Изменение пароля'; ?>
<h1>Изменение пароля</h1>
<div class="divider"></div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'passwd-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'password2'); ?>
		<?php echo $form->passwordField($model,'password2');?>
		<?php echo $form->error($model,'password2'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'newpasswd'); ?>
		<?php echo $form->passwordField($model,'newpasswd');?>
		<?php echo $form->error($model,'newpasswd'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'newpasswd2'); ?>
		<?php echo $form->passwordField($model,'newpasswd2');?>
		<?php echo $form->error($model,'newpasswd2'); ?>
	</div>
		
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


