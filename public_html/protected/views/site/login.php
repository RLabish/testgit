<?php
$this->pageTitle=Yii::app()->name . ' - Вход';
$this->breadcrumbs=array(
	'Login',
);
?>


<div id="login">

<div id="logo"><?php echo $this->pageTitle=Yii::app()->name; ?></div>

<h1>Регистрация</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>



	<div class="row error">
		<?php echo $form->error($model,'username'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>		

	<div class="row username">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
	</div>

	<div class="row password">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
	</div>
	
	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
	</div>

	
	<div class="row buttons">
	<?php 
		$a = $this->widget('zii.widgets.jui.CJuiButton', array(
				'name'=>'submit',
				'caption'=>'Войти',
  		));
	?>
	</div>
<?php /*	
	<div class="row">
	</div>		
*/ ?>

<?php $this->endWidget(); ?>
</div><!-- form -->

</div>