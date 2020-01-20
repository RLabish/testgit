<div id="tankRealStates-filter" class="filter form">
<?php
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); 
?>
	<div class="row">
		<?php echo CHtml::tag('label', array(), 'Период', true); ?>
		<?php
			$arr = date_parse(date(DATE_RSS));
			$year = $arr['year'];
			
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'model' => $model,
					'attribute' => 'dateFrom',					
					'options'=>array(
						'dateFormat'=>'dd.mm.yy',
						'changeYear'=> true,
						'showButtonPanel' => false,
						'yearRange' => ($year - 2).':'.$year,
					),
					'htmlOptions' => array ('class'=>'date')					
				));
			echo ' - ';
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'model' => $model,
					'attribute' => 'dateTo',					
					'options'=>array(
						'dateFormat'=>'dd.mm.yy',
						'changeYear'=> true,
						'showButtonPanel' => false,
						'yearRange' => ($year - 2).':'.$year,
					),
					'htmlOptions' => array ('class'=>'date'),					
				));
		?>
  	</div>	
  	
	<div class="row">
		<?php echo CHtml::tag('label', array(), 'АЗС', true); ?>
		<?php 
			if (isset($model->tank)) {
				$azs = new Azs();
				$azs->id = $model->tank->azsId;
			}
			else {
				$azs = Azs::model()->find(array('order'=>'name','condition'=>'exists (select 1 from tanks b where b.azsId = t.id and b.visible = 2)'));		
				if (!isset($azs)) $azs = new Azs(); 
			}
			echo $form->dropDownList($azs, 'id', 
				CHtml::listData(AZS::model()->findAll(array('order'=>'name','condition'=>'exists (select 1 from tanks b where b.azsId = t.id and b.visible = 2)')), 'id', 'name'), 
				array(
//				 'prompt' => '<все>',
//				 'options' => array($model->azsId=>array('selected'=>'selected')),
				 )
			); 
		?>
	</div>
	<div class="row">
		<?php echo CHtml::tag('label', array(), 'Резервуар', true); ?>
		<?php 
			echo $form->dropDownList($model, 'tankId', 
				CHtml::listData(Tanks::model()->findAll(array('condition'=>'azsId = :p and visible = 2','order'=>'name','params'=>array(':p'=>$azs->id))), 'id', 'name'), 
				array(
//				 'prompt' => '<все>',
//				 'options' => array($model->azsId=>array('selected'=>'selected')),
				 )
			); 
		?>
	</div>
  	
	<div class="row buttons">
		<?php
			$this->widget('zii.widgets.jui.CJuiButton', array(
					'name'=>'submit',
					'caption'=>'Применить',
  			));
  		?>
 	</div>
<?php 	$this->endWidget(); ?> 	
</div>


<script type="text/javascript">

	$("#Azs_id" ).change(function() {
		$("#TankRealStates_tankId").find('option').remove();
		jQuery.ajax({
			url: "index.php?r=tanks/tanksByAzs&azsId=" + $("#Azs_id" ).attr("value"),
			type: "POST",
			data: {ajaxData: "a"},  
			success: function (result) {
				var data = JSON.parse(result);
				for (var i in data) {
					var x = data[i];
					$('#TankRealStates_tankId').append('<option value="' +  x.id + '">' + x.label + '</option>');	
				}
			} 
		});
	});

</script>
