<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cardlimits-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php 
		echo $form->errorSummary($model);
		echo CHtml::hiddenField('CardLimits[cardId]', $model->cardId);			
	?>

	<table>
		<tbody>
			<tr>
				<td><?php echo $form->labelEx($model,'fuelId'); ?></td>
				<td><?php 
				  $list = array(''=>'') + CHtml::listData(Fuels::model()->findall(array('order' => 'name',)), 'id', 'name'); 
				  echo $form->dropDownList($model,'fuelId',	$list,array('class' => 'str-editor')); 
				?></td>
			</tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'limitType'); ?></td>
				<td><?php
				echo $form->dropDownList($model,'limitType', array(''=>'') + $model->limitTypeNames, array('class' => 'str-editor'));				
				?></td>
			</tr>						

			<tr>
				<td><?php echo $form->labelEx($model,'orderVolume'); ?></td>
				<td><?php echo $form->textField($model,'orderVolume',array('maxlength'=>8, 'class' => 'numeric-editor')); ?></td>
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
