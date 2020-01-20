<?php
/* @var $this SuppliersController */
/* @var $model Suppliers */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tanks-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model);	?>

	<table>
		<tbody>

			<tr>
				<td><?php echo $form->labelEx($model,'azsId'); ?></td>
				<td><?php 
				  $list = array(''=>'') + CHtml::listData(Azs::model()->findall(array('order' => 'name',)), 'id', 'name'); 
				  echo $form->dropDownList($model,'azsId',	$list,array('class' => 'str-editor')); 
				?></td>
			</tr>		

			<tr>
				<td><?php echo $form->labelEx($model,'name'); ?></td>
				<td><?php echo $form->textField($model,'name',array('class' => 'str-editor','maxlength'=>50)); ?></td>
			</tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'number'); ?></td>
				<td><?php echo $form->textField($model,'number',array('class' => 'numeric-editor','maxlength'=>10)); ?></td>
			</tr>


			<tr>
				<td><?php echo $form->labelEx($model,'terminalId'); ?></td>
				<td><?php 
				  $list = array(''=>'') + CHtml::listData(Terminals::model()->findall(array('order' => 'name',)), 'id', 'name'); 
				  echo $form->dropDownList($model,'terminalId',	$list,array('class' => 'str-editor')); 
				?></td>
			</tr>		

			<tr>
				<td><?php echo $form->labelEx($model,'fuelId'); ?></td>
				<td><?php 
				  $list = array(''=>'') + CHtml::listData(Fuels::model()->findall(array('order' => 'name',)), 'id', 'name'); 
				  echo $form->dropDownList($model,'fuelId',	$list,array('class' => 'str-editor')); 
				?></td>
			</tr>		
			
			<tr>
				<td><?php echo $form->labelEx($model,'capacity'); ?></td>
				<td><?php echo $form->textField($model,'capacity',array('class' => 'numeric-editor')); ?></td>													
			<tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'maxVolume'); ?></td>
				<td><?php echo $form->textField($model,'maxVolume',array('class' => 'numeric-editor')); ?></td>													
			<tr>

			<tr>
				<td><?php echo $form->labelEx($model,'visible'); ?></td>
				<td><?php echo $form->dropDownList($model,'visible', array(1 => 'Нет', 2 => 'Да',), array('class' => 'str-editor') );	?></td>
			</tr>

			<?php /*
			<tr>
				<td><?php echo $form->labelEx($model,'note'); ?></td>
				<td><?php echo $form->textField($model,'note',array('class' => 'strbig-editor','maxlength'=>255)); ?></td>
														
			<tr>
				<td colspan="2">					
					<p class="note">Поля с <span class="required">*</span> обязательны для заполнения.</p>
				</td>
			</tr>
			*/ ?>

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
