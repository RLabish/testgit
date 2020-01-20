<div id="sumsales-filter" class="filter form">
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
			echo $form->dropDownList($model, 'azsId', 
				CHtml::listData(Azs::model()->findAll(array('order'=>'name')), 'id', 'name'), 
				array(
				 'prompt' => '<все>',
				 'options' => array($model->azsId=>array('selected'=>'selected')),
				 )
			); 
		?>
	</div>
  	
	<div class="row">
		<?php echo CHtml::tag('label', array(), 'Группы', true); ?>
		<?php 
		$items = array();
		foreach ($groupDefs as $key => $value)
			$items[$key] = $value['header'];
		echo CHtml::dropDownList('group1', isset($_GET['group1']) ? $_GET['group1'] : '',  $items);
		echo CHtml::dropDownList('group2', isset($_GET['group2']) ? $_GET['group2'] : '',  array_merge(array('none'=>'<нет>',),$items));
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
