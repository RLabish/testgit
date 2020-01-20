<?php
/* @var $this SuppliersController */
/* @var $model Suppliers */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'suppliers-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model);	?>

	<table>
		<tbody>

			<tr>
				<td><?php echo $form->labelEx($model,'code'); ?></td>
				<td><?php echo $form->textField($model,'code',array('class' => 'str-editor','maxlength'=>10)); ?></td>
			</tr>

			<tr>
				<td><?php echo $form->labelEx($model,'name'); ?></td>
				<td><?php echo $form->textField($model,'name',array('class' => 'str-editor','maxlength'=>50)); ?></td>
			</tr>
			
			
			<tr>
				<td><?php echo $form->labelEx($model,'note'); ?></td>
				<td><?php echo $form->textField($model,'note',array('class' => 'strbig-editor','maxlength'=>255)); ?></td>
														
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
					<?php /*
						$this->widget('zii.widgets.jui.CJuiButton', array(
							'name'=>'btn-card-refill',
							'caption'=>'Пополнить',
  						));
  					 */	
  					?>
					</div>
			    </td>
			</tr>			
		</tbody>
	</table>
	<?php echo $form->hiddenField($model,'controlBalance',array('value'=>'1')); ?>
	

<?php $this->endWidget(); ?>
</div>
