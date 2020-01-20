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
				<td><?php echo $form->labelEx($model,'terminalId'); ?></td>
				<td><?php 
				  $list = array(''=>'') + CHtml::listData(Terminals::model()->findall(array('order' => 'name',)), 'id', 'name'); 
				  echo $form->dropDownList($model,'terminalId',	$list,array('class' => 'str-editor')); 
				?></td>
			</tr>		

			
			<tr>
				<td><?php echo $form->labelEx($model,'pumpNo'); ?></td>
				<td><?php echo $form->textField($model,'pumpNo',array('class' => 'numeric-editor','maxlength'=>10)); ?></td>
			</tr>

			<tr>
				<td><?php echo $form->labelEx($model,'nozzleNo'); ?></td>
				<td><?php echo $form->textField($model,'nozzleNo',array('class' => 'numeric-editor','maxlength'=>10)); ?></td>
			</tr>


			<tr>
				<td><?php echo $form->labelEx($model,'tankId'); ?></td>
				<td><?php 
				  $list = array(''=>'') + CHtml::listData(Tanks::model()->findall(array('order' => 'name','condition'=>'t.azsId = :azs_id', 'params'=>array('azs_id'=>$model->azsId))), 'id', 'name'); 
				  echo $form->dropDownList($model,'tankId',	$list,array('class' => 'str-editor')); 
				?></td>
			</tr>


			<?php /* <tr>
				<td><?php echo $form->labelEx($model,'note'); ?></td>
				<td><?php echo $form->textField($model,'note',array('class' => 'strbig-editor','maxlength'=>255)); ?></td>
			</tr>*/?>
			
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

<script type="text/javascript">

	$("#Pumps_azsId" ).change(function() {
		$("#Pumps_tankId").find('option').remove();
		jQuery.ajax({
			url: "index.php?r=tanks/tanksByAzs&azsId=" + $("#Pumps_azsId" ).attr("value"),
			type: "POST",
			data: {ajaxData: "a"},  
			success: function (result) {
				var data = JSON.parse(result);
				$('#Pumps_tankId').append('<option value=""></option>');	
				for (var i in data) {
					var x = data[i];
					$('#Pumps_tankId').append('<option value="' +  x.id + '">' + x.label + '</option>');	
				}
			} 
		});
	});

</script>
