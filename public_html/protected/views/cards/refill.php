<?php 
  $card=Cards::model()->findByPk($_GET['id']); 
  $this->pageTitle = 'Пополнение карты  #'.$card->number.' /'.$card->owner.'/';
?>

<div class="header">
	<h1><?php echo $this->pageTitle ?></h1>
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cards-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php
	 echo $form->errorSummary($model);
     echo CHtml::hiddenField('CardOperations[cardId]', $card->id);
     echo CHtml::hiddenField('CardOperations[operationType]', 1);	  
	?>

	<table>
		<tbody>		
			<tr>
				<td><?php echo $form->labelEx($model,'refillDescription'); ?></td>
				<td><?php echo $form->textField($model,'refillDescription',array('size'=>60, 'class' => 'str-editor')); ?></td>
			</tr>		

			<tr>
				<td><?php echo $form->labelEx($model,'fuelId'); ?></td>
				<td><?php 
				  $list = CHtml::listData(Fuels::model()->findall(array('order' => 'name',)), 'id', 'name'); 
				  echo $form->dropDownList($model,'fuelId',	$list,array('class' => 'str-editor')); 
				?></td>
			</tr>		

			<tr>
				<td><?php echo $form->labelEx($model,'volume'); ?></td>
				<td><?php echo $form->textField($model,'volume',array('maxlength'=>8, 'class' => 'number-editor')); ?></td>
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
							'caption'=>'Пополнить',
  						));
  					?>
					</div>
			    </td>
			</tr>			
		</tbody>
	</table>
	

<?php $this->endWidget(); ?>
</div>

