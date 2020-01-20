<?php 
  $this->pageTitle = 'Лимиты';
?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-income-create',
							'buttonType'=>'button',
							'caption'=>'Новый лимит',
							'htmlOptions'=>array('class'=>'btn-add'),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("cardLimits/create", array('cardId'=>$model->cardId)).'";return false;}',
						)
				 );
  		?>
	</div>
</div>

<?php
 global $limitNpp;
 $limitNpp = 1;
 function columnNumber($row) {
 	global $limitNpp;
 	return	'<a href="'.Yii::app()->createUrl("cardLimits/update",array("id"=>$row->id)).'" title="Изменить">'.$row->card->number.'-'.$limitNpp++.'</a>';	
}

function columnExpire($row) {
	if ($row->ok) {
		$msg = Yii::app()->DateFormatter->formatDateTime($row->expire,'medium',null);
		return '<div style="text-align:center">'.$msg.'</div>';
	}			
	else 
		return '<div style="color:red;text-align:center">'.$row->stateAsText.'</div>';
}
		
$this->widget('application.extensions.MyGridView', array(
	'id'=>'cardlimits-grid',
	'dataProvider'=>$model->search(),
//	'filter'=>$model,
	'columns'=> array(	
			array(            
        		'header'=>'ID',
            	'value'=>'columnNumber($data)',
        		'type' =>'raw',
        	),
			array(            
        		'header'=>'Продукт',
            	'name'=>'fuel.name',
        	),
			array(            
        		'header'=>'Тип лимита',
            	'value'=>'$data->limitTypeName()',
        		'type' =>'raw',
        	),
			array(            
        		'header'=>'Объем, л',
            	'name'=>'orderVolume',
				'htmlOptions'=>array('class'=>'number-cell'),
        	),		
	),
	'summaryCssClass' => 'grid-view-toolbar',
	'summaryText'=>	'',//{start}-{end} из {count}',
));
?>
