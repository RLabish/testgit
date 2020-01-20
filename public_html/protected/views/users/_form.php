<?php
/* @var $this SuppliersController */
/* @var $model Suppliers */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model);	?>
		
	<table>
		<tbody>
			<tr>
				<td><?php echo $form->labelEx($model,'username'); ?></td>
				<td><?php echo $form->textField($model,'username',array('class' => 'str-editor','maxlength'=>50)); ?></td>
			</tr>
			
			<?php if ($model->isNewRecord) {?>
				<tr>
					<td><?php echo $form->labelEx($model,'passwd'); ?></td>
					<td><?php echo $form->passwordField($model,'passwd'); ?></td>
				</tr>
				<tr>
					<td><?php echo $form->labelEx($model,'passwd2'); ?></td>
					<td><?php echo $form->passwordField($model,'passwd2'); ?></td>
				</tr>
			<?php } else {?>
				<tr>
					<td><?php echo $form->labelEx($model,'passwd'); ?></td>
					<td><?php echo CHtml::link('изменить', array('newpassword','id'=>$model->id)); ?></td> 
				</tr>				
			<?php } ?>

			<tr>
				<td><?php echo $form->labelEx($model,'fullname'); ?></td>
				<td><?php echo $form->textField($model,'fullname',array('class' => 'str-editor','maxlength'=>50)); ?></td>
			</tr>			
			
			<tr>
				<td><?php echo $form->labelEx($model,'description'); ?></td>
				<td><?php echo $form->textField($model,'description',array('class' => 'strbig-editor','maxlength'=>255)); ?></td>
			</tr>
				
			<tr>
				<td><?php echo $form->labelEx($model,'role'); ?></td>
				<td><?php echo $form->dropDownList($model,'role',array_merge(array('banned' => 'заблокирован'), $model->roleNames()), array('class' => 'str-editor')); ?></td>
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

 $js_vars = "var roles = {}; \n";
 foreach ($model->roleNames() as $k => $v) {
   $arr = array();
   foreach ($model->roleRights($k) as $x) 
	$arr[] = '"'.$x.'"';
   $js_vars = $js_vars."roles.$k = {};\n"."roles.$k.checked = [".implode(',', $arr)."];\n";
   }

 Yii::app()->clientScript->registerScript('',"\n\n$js_vars".'

$(function() {
	update_role_refs(false);
});
	
function check_role_right(role, right) {
	for (var i = 0; i < roles[role].checked.length; i++)
		if (right == roles[role].checked[i])
			return true;
	return false;
}

function update_role_refs(bydef) {
	var role = $("#Users_role" ).attr("value");

	var elems = $("#Users_rights input" );
	for (var i = 0; i < elems.length; i++) {
		var right = elems[i].value;
		if (role == "banned") {
			elems[i].checked = false;
			elems[i].disabled = true;		
			continue;
		}
		f = check_role_right(role, right);
		if (bydef)
			elems[i].checked = f;
		else
			elems[i].checked = f || elems[i].checked;
		elems[i].disabled = f;
	}
	
}
	
$("#Users_role" ).change(function() {
	update_role_refs(true);
});

    ',CClientScript::POS_READY);
 
 
?> 


