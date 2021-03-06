<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cards-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php
	 
		echo $form->errorSummary($model);
	?>


	<table>
		<tbody>
			<tr>
				<td><?php echo $form->labelEx($model,'number'); ?></td>
				<td><?php 
						if ($model->isNewRecord)
		  					echo $form->textField( $model,'number',array('maxlength'=>20, 'class' => 'str-editor'));
		  				else 
		  					echo $form->textField($model,'number',array('readonly'=>'readonly', 'class' => 'str-editor'));
				 ?></td>
			</tr>

			<tr>
				<td><?php echo $form->labelEx($model,'ownerType'); ?></td>
				<td><?php echo $form->dropDownList($model,'ownerType', $model->ownerTypeNames, array('class' => 'str-editor') );	?></td>
			</tr>
			
			<?php if (in_array('tankToId', $model->attributeNames())) { ?>
			<tr>
				<td><?php echo $form->labelEx($model,'typeExt'); ?></td>
				<td><?php echo $form->dropDownList($model,'typeExt', $model->typeExtNames, array('class' => 'strbig-editor') );	?></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td><?php echo $form->labelEx($model,'type'); ?></td>
				<td><?php echo $form->dropDownList($model,'type', $model->typeNames, array('class' => 'str-editor') );	?></td>
			</tr>						
			<?php } ?>
			
			<tr>
				<td><?php echo $form->labelEx($model,'pin'); ?></td>
				<td><?php echo $form->textField( $model,'pin',array('maxlength'=>4, 'class' => 'str-editor'));	?></td>
			</tr>					
								
			<tr>
				<td><?php echo $form->labelEx( $model,'owner'); ?>
				</td>
				<td><?php 
		  			$crit = new CDbCriteria;
					$crit->distinct = true;
					$crit->select = "owner";
					$crit->order = "owner";
					//$crit->condition = "type = ".$model->type;
					$table = new Cards;
					$rows = $table->findAll($crit);
					$src = array();
					while (list($key, $val) = each($rows)) {
	    				$src[$key]=$val->owner;
					}	
					
      				$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
      					'model'=>$model,
      					'attribute'=>'owner',
  	  					'source'=>$src,
						'options'=>array(
          					'minLength'=>'1',
      	  					'autoFill'=>'true',
      					),
      					'htmlOptions'=>array('class' => 'str-editor'),
      				));
  					
  				  ?>
  				</td>
			</tr>
			<tr>
				<td><?php echo $form->labelEx($model,'description'); ?></td>
				<td><?php echo $form->textField($model,'description',array('maxlength'=>60, 'class' => 'strbig-editor')); ?></td>
			</tr>		

			<tr>
				<td><?php echo $form->labelEx($model,'state'); ?></td>
				<td><?php echo $form->dropDownList($model,'state',$model->cardStates, array('class' => 'str-editor')); ?></td>
			</tr>		
			
			<tr>
				<td><?php echo $form->labelEx($model,'expireFmt'); ?></td>
				<td><?php 				
						$arr = date_parse(date(DATE_RSS));
						$year = $arr['year'];
						$this->widget('zii.widgets.jui.CJuiDatePicker', array(
							'model' => $model,
							'attribute' => 'expireFmt',
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
