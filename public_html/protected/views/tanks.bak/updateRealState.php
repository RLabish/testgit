<?php $this->pageTitle = 'Ввод замера' ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>


<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tankincome-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model);	?>
	<table>
		<tbody>		
			<tr>
				<td><?php echo $form->labelEx($model,'id'); ?></td>
				<td><?php echo $form->dropDownList($model,'id', Tanks::listData("azs", true), array('class' => 'strbig-editor'));?></td>
			</tr>		
					
			<tr>
				<td><?php echo $form->labelEx($model,'realFuelLevel'); ?></td>
				<td><?php echo $form->textField($model,'realFuelLevel',array('class' => 'numeric-editor', 'maxlength'=>8)); ?></td>
			</tr>					
						
			<tr>
				<td><?php echo $form->labelEx($model,'realFuelVolume'); ?></td>
				<td><?php echo $form->textField($model,'realFuelVolume',array('class' => 'numeric-editor', 'maxlength'=>8)); ?></td>
			</tr>					
														
			<tr>
				<td><?php echo $form->labelEx($model,'realTemperature'); ?></td>
				<td><?php echo $form->textField($model,'realTemperature',array('class' => 'numeric-editor', 'maxlength'=>8)); ?></td>
			</tr>					

			<tr>
				<td><?php echo $form->labelEx($model,'realDensity'); ?></td>
				<td><?php echo $form->textField($model,'realDensity',array('class' => 'numeric-editor', 'maxlength'=>8)); ?></td>
			</tr>					

			<tr>
				<td><?php echo $form->labelEx($model,'realFuelMass'); ?></td>
				<td><?php echo $form->textField($model,'realFuelMass',array('class' => 'numeric-editor', 'maxlength'=>8)); ?></td>
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

<?php 
    Yii::app()->clientScript->registerScript('','
	
$(function() {
	if ($("#Tanks_id" ).attr("value") == "") 
		$(".numeric-editor").attr({ disabled: ""}); 
});
	
$("#Tanks_id" ).change(function() {
	var tankId = $("#Tanks_id" ).attr("value");
	if (tankId == "") {
		$(".numeric-editor").attr({ disabled: ""});
	}
	else {
		$(".numeric-editor").removeAttr("disabled");  
	}
	$(".numeric-editor").attr({value: ""});  
});

$("#Tanks_realFuelLevel" ).keyup(function() {
	var tankId = $("#Tanks_id" ).attr("value");
	var level = $("#Tanks_realFuelLevel" ).attr("value");
	jQuery.ajax({
		url: "index.php?r=tanks/levelToVolume&id=" + tankId + "&level=" + level,
		type: "POST",
		data: {ajaxData: "a"},  
		success: function (result) {$("#Tanks_realFuelVolume").attr({value: result});} 
	});

function calcmass() {
    var volume = $("#Tanks_realFuelVolume").attr("value");
	var density = $("#Tanks_realDensity").attr("value");
	var mass = 0;
	if ((volume != "") && (density != "")) {
		mass = parseFloat(volume) * parseFloat(density);
		if (isNaN(mass))
			mass = 0;
	}
	$("#Tanks_realFuelMass").attr({value: mass.toFixed(0)});
	
}
	
$("#Tanks_realFuelVolume" ).keyup(function() {
	calcmass();
});

$("#Tanks_realDensity" ).keyup(function() {
	calcmass();
});
				
});

    ',CClientScript::POS_READY);
 
 
?>
