<div id="page-card-view">
<div class="header">
	<h1>Карта <?php echo '#'.$model->number; ?></h1>
	<div class="buttons">
		<?php
		 $this->pageTitle = 'Карта #'.$model->number;
		 if ($model->type == Cards::TYPE_DEBIT) {
			$this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-card-refill',
							'buttonType'=>'button',
					 		'caption'=>'Пополнить',
							'htmlOptions'=>array('class'=>'btn-bold'),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("cards/refill", array('id'=>$model->id)).'";return false;}',
						)
			);				 
		 }	
/*
		 $this->widget('zii.widgets.jui.CJuiButton',
		 		array(
		 				'name'=>'btn-card-del',
		 				'caption'=>'Удалить',
		 				'htmlOptions'=>array('class'=>'btn-delete', 'title'=>'Удалить'),
		 				'onclick'=>
		 				'js:function(){'
		 				.'	if(confirm("Удалить карту?")) {'
		 				.'location.href = \''.Yii::app()->createUrl("cards/delete", array('id'=>$model->id)).'\';'
		 				.'		return false;'
		 				.'	} else return false;'
		 				.'}'
		 
		 		)
		 );
*/		 		 
  		?>
	</div>
</div>


<div id="detail-view">

<h2>Регистрационная информация</h2>
<div id="detail-view-toolbar">
<?php 
echo CHtml::link('изменить', array('update', 'id'=>$model->id,));
echo CHtml::linkButton('удалить', array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Удалить карту?'));
?>		  		
</div>

<div id="card-reginfo">

<?php 
function columnExpire($row) {
	if ($row->ok) 
		return '<div>'.$row->stateAsText.'</div>';
	else 
		return '<div style="color:red">'.$row->stateAsText.'</div>';
}

 $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'number',
		'owner',
 		'description',
        array(
        	'label'=>'Тип',
 			'type'=>'raw',
        	'value'=>$model->typeNames[$model->type],
         ),
        array(
        	'label'=>'Состояние',
 			'type'=>'raw',
        	'value'=>columnExpire($model),
         ),
        array(
        	'label'=>'Действует до',
 			'type'=>'raw',
        	'value'=>$model->expireFmt,
         ), 		
 	),
)); 

?>
</div>

<?php if ($model->type == Cards::TYPE_LIMITED) { ?>
<h2>Лимиты</h2>
<div id="card-limits-toolbar">
<?php echo CHtml::link('изменить', array('cardLimits/index', 'cardId'=>$model->id,)); ?>
</div>

<div id="card-limits">
<table id="yw0" class="detail-view">
<tbody>
<?php
	$crit = new CDbCriteria;
	$crit->compare('t.cardId', $model->id);
	$crit->with = array('fuel');
	$crit->order = 'fuel.code, limitType';
	$limits = CardLimits::model()->findAll($crit);
	if (count($limits) == 0) {
		echo '<tr class="odd empty">';
		echo '<th> <нет></th>';
		echo '<td></td>';
		echo '</tr>';		
	}
	else {
		$i = 1;
		foreach($limits as $lim) {
			echo '<tr class="'.(($i % 2 == 0)? 'even':'odd').'">';
			echo '<th>'.$lim->fuel->name.'</th>';
			echo '<td class="col-name col-1">'.$lim->limitTypeName().'</td>';
			echo '<td class="col-volume col-2">'.$lim->orderVolume.'</td>';
			echo '<td></td>';
			echo '</tr>';
			$i++;
		}
	}
 ?> 
</tbody>
</table>
</div>
<?php } ?>

 
<h2>К выдаче, л</h2>
<div id="card-balance">
<table id="yw1" class="detail-view">
<tbody>
<?php
$fuels	= Fuels::model()->findAll(array('select'=>'id,name', 'order'=>'code',));
$i = 1;
foreach($fuels as $f) {
	echo '<tr class="'.(($i % 2 == 0)? 'even':'odd').'">';
	echo '<th>'.$f->name.'</th>';
	echo '<td class="col-volume col-1">'.number_format($model->balanceByFuelId($f->id), 2, ".", " ").'</td>';
	echo '<td></td>';
	echo '</tr>';
	$i++;	
}
?>
</tbody>
</table>
</div>


</div>

</div>
