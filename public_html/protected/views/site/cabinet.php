<?php $this->pageTitle = 'Личный кабинет'; ?>
<h1>Личный кабинет</h1>
<div class="divider"></div>

<?php /*<h2>Регистрационная информация</h2>*/ ?>

<div class="form cabinet">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contractors-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<table>
		<tbody>
			<tr>
				<td><?php echo $form->labelEx($model,'fullname'); ?></td>
				<td><?php echo $form->textField($model,'fullname', array('disabled'=>'1', 'class' => 'str-editor')); ?></td>
				<td/>
			</tr>
				
			<tr>
				<td><?php echo $form->labelEx($model,'username'); ?></td>
				<td><?php echo $form->textField($model,'username', array('disabled'=>'1', 'class' => 'str-editor')); ?></td>
			</tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'curpasswd'); ?></td>
				<td><?php echo $form->passwordField($model,'curpasswd', array('class' => 'str-editor')); ?></td>
			</tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'newpasswd'); ?></td>
				<td><?php echo $form->passwordField($model,'newpasswd', array( 'class' => 'str-editor')); ?></td>
			</tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'newpasswd2'); ?></td>
				<td><?php echo $form->passwordField($model,'newpasswd2', array('class' => 'str-editor')); ?></td>
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
