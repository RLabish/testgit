<?php $this->pageTitle = 'Фактические остатки'; ?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-income-create',
							'buttonType'=>'button',
							'caption'=>'Ввод замера',
							'htmlOptions'=>array('class'=>'btn-add'),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("tanks/updaterealstate").'";return false;}',
						)
				 );
  		?>
	</div>
</div>

<?php

 
$this->widget('application.extensions.MyGridView', array(
	'id'=>'tankReaStates-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'header'=>'АЗС',
			'name'=>'azsId',
			'value'=>'$data->azs->name',
			'filter'=>CHtml::listData(Azs::model()->findall(array('order' => 'name',)), 'id', 'name'),				
		),			
		array(
			'header'=>'Резервуар',
			'name'=>'id',
			'value'=>'$data->name',
			'filter'=>CHtml::listData(Tanks::model()->findall(array('order' => 'name',)), 'id', 'name'),				
		),			
		array(
			'header'=>'Вид топлива',
			'name'=>'fuelId',
			'value'=>'$data->fuel->name',
			'filter'=>CHtml::listData(Fuels::model()->findall(array('order' => 'name',)), 'id', 'name'),				
		),
		array(
			'name' => 'realLevel',
			'header' => 'Уровень,мм',				
			'value'=>'isset($data->realState) ? number_format($data->realFuelLevel, 1, ".", " ") : ""',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'realVolume',
			'header' => 'Объем,л',				
			'value'=>'isset($data->realState) ? number_format($data->realFuelVolume, 0, ".", " ") : ""',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'realTemperature',
			'header' => 'Температура,*С',				
			'value'=>'isset($data->realState) ? number_format($data->realTemperature, 1, ".", " ") : ""',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'realDensity',
			'header' => 'Плотность,г/cм3',				
			'value'=>'isset($data->realState) ? number_format($data->realDensity, 4, ".", " ") : ""',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'realMass',
			'header' => 'Масса,кг',				
			'value'=>'isset($data->realState) ? number_format($data->realFuelMass, 1, ".", " ") : ""',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		/*
		array(
			'name' => 'oldVolume',
			'header' => 'Остаток,л',
			'value'=>'sprintf("%3.0f", $data->oldVolume)',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'operVolume',
			'header' => 'По факту|Топливо,л',				
			'value'=>'sprintf("%3.0f", $data->operVolume)',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'restVolume',
			'header' => 'По факту|Мертвый ост.,л',				
			'value'=>'sprintf("%3.0f", $data->operRest)',
			'filter' => false,
			'htmlOptions'=>array('class'=>'number-cell'),				
		),
		array(
			'name' => 'volumeDiff',
			'header' => 'Разница,л',				
			'value'=>'sprintf("%3.0f", $data->volumeDiff)',
			'filter' => false,
			'cssClassExpression'=>'$data->volumeDiff < 0 ? "number-cell error-cell": "number-cell"',
		),
			*/
	),
));
?>
