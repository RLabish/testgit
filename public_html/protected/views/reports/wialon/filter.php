<div id="wialon-filter" class="filter form">
<?php
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); 
?>
	<div class="row">
		<?php echo CHtml::tag('label', array(), 'Период', true); ?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$arr = date_parse(date(DATE_RSS));
			$year = $arr['year'];			
			$this->widget('CJuiDateTimePicker', array(
					'model' => $model,
					'attribute' => 'dateFrom',					
					'options'=>array(
						'dateFormat'=>'dd.mm.yy',
						'timeFormat'=>'hh:mm:ss',
						'changeYear'=> true,
						'showButtonPanel' => false,
						'yearRange' => ($year - 2).':'.$year,
					),
					'htmlOptions' => array ('class'=>'datetime'),
				));
			echo ' - ';
			$this->widget('CJuiDateTimePicker', array(
					'model' => $model,
					'attribute' => 'dateTo',
					'options'=>array(
						'dateFormat'=>'dd.mm.yy',
						'timeFormat'=>'hh:mm:ss',
						'changeYear'=> true,
						'showButtonPanel' => false,
						'yearRange' => ($year - 2).':'.$year,
					),
					'htmlOptions' => array ('class'=>'datetime'),
			));
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


