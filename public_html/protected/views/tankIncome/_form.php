<div class="form">

<?php
 Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');

 $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tankincome-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model);	?>
	<table>
		<tbody>
			<tr>
				<td><?php echo $form->labelEx($model,'dateFmt'); ?></td>
				<td><?php 		
					if ($model->tableSchema->columns['date']->dbType == 'date') {
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
					}
					else {
						$arr = date_parse(date(DATE_RSS));
						$year = $arr['year'];			
						$this->widget('CJuiDateTimePicker', array(
								'model' => $model,
								'attribute' => 'dateFmt',
								'options'=>array(
									'dateFormat'=>'dd.mm.yy',
									'timeFormat'=>'hh:mm',
									'changeYear'=> true,
									'showButtonPanel' => false,
									'yearRange' => ($year - 2).':'.$year,
								),
								'htmlOptions' => array ('class'=>'datetime'),
							));
					}
				?></td>
			</tr>
			
			<tr>
				<td><?php echo $form->labelEx($model,'tankId'); ?></td>
				<td><?php echo $form->dropDownList($model,'tankId', Tanks::listData("azs"), array('class' => 'strbig-editor'));?></td>
			</tr>		
					
			<tr>
				<td><?php echo $form->labelEx($model,'supplierName'); ?>	</td>
				<td><?php 
					$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
							'model'=>$model,
							'attribute'=>'supplierName',
							'source' =>Yii::app()->createUrl('suppliers/autocompleteByName'),
							'options'=>array(
									'minLength'=>'1',
									'showAnim'=>'fold',
								/*	'select' =>'js: function(event, ui) {
										this.value = ui.item.label;
										$("#Tasks_driverId").val(ui.item.id);
										return false;
									}',
									*/
							),
							'htmlOptions' => array('class' => 'str-editor', 'maxlength'=>50,),
					));
  				  ?>
  				</td>
			</tr>
						
			<tr>
				<td><?php echo $form->labelEx($model,'doc'); ?></td>
				<td><?php echo $form->textField($model,'doc',array('class' => 'strbig-editor', 'maxlength'=>255)); ?></td>
			</tr>
						
			<tr>
				<td><?php echo $form->labelEx($model,'volume'); ?></td>
				<td><?php echo $form->textField($model,'volume',array('class' => 'numeric-editor', 'maxlength'=>8)); ?></td>
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
