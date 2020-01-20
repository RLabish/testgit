<?php
/* @var $this TankInventoryController */
/* @var $model TankInventory */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tankinventory-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model);	?>
	<table>
		<tbody>
			<tr>
				<td><?php echo $form->labelEx($model,'dateFmt'); ?></td>
				<td><?php 				
						$arr = date_parse(date(DATE_RSS));
						$year = $arr['year'];
						$this->widget('zii.widgets.jui.CJuiDatePicker', array(
							'model' => $model,
							'attribute' => 'dateFmt',
//							'language'=>'ru',
							'options'=>array(
								'showAnim'=>'fold',
								'dateFormat'=>'dd.mm.yy',
								'changeYear'=> true,
								'showButtonPanel' => true,
								'yearRange' => $year.':'.($year + 5),				
							),
							'htmlOptions'=>array(
							),
						));				
				?></td>
			</tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'tankId'); ?></td>
				<td><?php echo $form->dropDownList($model,'tankId', Tanks::listData("azs", true), array('class' => 'strbig-editor'));?></td>				
			</tr>		
										
			<tr>
				<td><?php echo $form->labelEx($model,'doc'); ?></td>
				<td><?php echo $form->textField($model,'doc',array('class' => 'strbig-editor', 'maxlength'=>255)); ?></td>
			</tr>
						
			<tr>
				<td><?php echo $form->labelEx($model,'operVolume'); ?></td>
				<td><?php echo $form->textField($model,'operVolume',array('class' => 'numeric-editor', 'maxlength'=>8)); ?></td>
			</tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'operRest'); ?></td>
				<td><?php echo $form->textField($model,'operRest',array('class' => 'numeric-editor', 'maxlength'=>8)); ?></td>
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
