<div id="shift-filter" class="filter form">

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
					'name' => 'dateFrom',
					'value' => $dateFrom,					
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
					'name' => 'dateTo',
					'value' => $dateTo,					
					'options'=>array(
//							'showAnim'=>'fold',
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
			echo CHtml::dropDownList('azsId', $azsId, 
				CHtml::listData(Azs::model()->findAll(), 'id', 'name')//, 
//				array(
//				 'prompt' => '<все>',
//				 'options' => array($model->azsId=>array('selected'=>'selected')),
//				 )
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
