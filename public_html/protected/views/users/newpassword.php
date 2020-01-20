<?php $this->pageTitle = 'Изменение пароля пользователя "'.$model->username.'"'; ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
	<div1 class="toolbar" />
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form-psw',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model);	?>
		
	<table>
		<tbody>			
			<tr>
				<td><?php echo $form->labelEx($model,'passwd'); ?></td>
				<td><?php echo $form->passwordField($model,'passwd'); ?></td>
			</tr>
			<tr>
				<td><?php echo $form->labelEx($model,'passwd2'); ?></td>
				<td><?php echo $form->passwordField($model,'passwd2'); ?></td>
			</tr>

			<tr>
				<td colspan="2">					
					<p class="note">Поля с <span class="required">*</span> обязательны для заполнения.</p>
				</td>
			</tr>
			
			<tr><td colspan="2">								
					<div class="buttons">
					<?php 
						$this->widget('zii.widgets.jui.CJuiButton', array(
							'name'=>'submit',
							'caption'=>'Сохранить',
  						));
  						
  					?>
					</div>
			    </td>
			</tr>			
		</tbody>
	</table>
	

<?php $this->endWidget(); ?>
</div>

