<div id="page-card-view">
<div class="header">
	<h1>Подразделение "<?php echo $model->name; ?>"</h1>
	<div class="buttons">
		<?php 
			$this->pageTitle = 'Подразделение '.$model->name;
			if (Yii::app()->user->checkAccess('organizations.limits')) {
				$this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-client-refill',
							'buttonType'=>'button',
					 		'caption'=>'Пополнить',
							'htmlOptions'=>array('class'=>'btn-bold'),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("organizations/refill", array('id'=>$model->id)).'";return false;}',
						)
				);		
			}
  		?>
	</div>
</div>


<div id="detail-view">

<h2>Регистрационная информация</h2>
<div id="detail-view-toolbar">
<?php 
if (Yii::app()->user->checkAccess('organizations.update')) {
	echo CHtml::link('изменить', array('update', 'id'=>$model->id,));
	echo CHtml::linkButton('удалить', array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Удалить клиента?'));
}
?>		  		
</div>

<div id="card-reginfo">

<?php 

 $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'name',
 		'description',
 	),
)); 

?>
</div>

</div>


</div>

</div>
