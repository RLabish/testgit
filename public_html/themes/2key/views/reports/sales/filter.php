<div id="sales-filter" class="filter form">

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
					'htmlOptions' => array ('class'=>'date'),
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
		<?php 
			echo CHtml::tag('label', array(), 'Водитель', true);
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'model'=>$model,
      				'attribute'=>'driverName',
					'source' =>Yii::app()->createUrl('cards/autoCompleteByDriver'),
					'options'=>array(
							'minLength'=>'1',
					),
			));
  		?>
  	</div>

	<div class="row">
		<?php 
			echo CHtml::tag('label', array(), 'Авто', true);
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'model'=>$model,
      				'attribute'=>'autoName',
					'source' =>Yii::app()->createUrl('cards/autoCompleteByAuto'),
					'options'=>array(
							'minLength'=>'1',
					),
			));			
  		?>
  	</div>

	<div class="row buttons">
		<?php /*echo CHtml::submitButton('Search'); */?>
		<?php
			$this->widget('zii.widgets.jui.CJuiButton', array(
					'name'=>'submit',
					'caption'=>'Применить',
  			));
  		?>
 	</div>
<?php 	$this->endWidget(); ?> 	
</div>
