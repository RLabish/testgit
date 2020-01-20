<?php 
  $this->pageTitle = 'Карты';
?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-card-create',
							'buttonType'=>'button',
							'caption'=>'Новая карта',
							'htmlOptions'=>array('class'=>'btn-add'),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("cards/create").'";return false;}',
						)
				 );
  		?>
	</div>
</div>

<div class="toolbar grid-toolbar">
	<?php $this->widget('zii.widgets.jui.CJuiButton',
			array(
					'name'=>'btn-xls',
					'buttonType'=>'button',
					'htmlOptions'=>array('class'=>'tb-btn xls-tb-btn', 'title'=>'excel'),
					'onclick'=>'js:function(){gridtoexcel("cards-grid","'.$this->createUrl('excel').'");}',
			)
	);
	?>
</div>

<?php
 function columnNumber($row) {
 	return	'<a href="'.Yii::app()->createUrl("cards/view",array("id"=>$row->id)).'" title="Изменить">'.$row->number.'</a>';	
}

function columnExpire($row) {
	if ($row->ok) {
		$msg = Yii::app()->DateFormatter->format('dd.MM.yyyy',$row->expire);
		return '<div style="text-align:center">'.$msg.'</div>';
	}			
	else 
		return '<div style="color:red;text-align:center">'.$row->stateAsText.'</div>';
}

 function columnBalance($row, $fuelId) {
	$x = $row->balanceByFuelId($fuelId);
	if ($x > 0)
		return number_format($x, 2, ".", " ");
	else 
		return '';
}
				
$this->widget('application.extensions.MyGridView', array(
	'id'=>'cards-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=> array_merge(
		array(	
			array(            
            	'name'=>'number',
        		'header'=>'№',
            	'value'=>'columnNumber($data)',
        		'headerHtmlOptions' => array('width'=>'70', ), 
        		'type' =>'raw',
        	),
           	'owner',
        	'description'			
        	,array(            
            	'name'=>'expire',
//        		'header'=>'Действует до',
        		'header'=>'до',
        		'value'=>'columnExpire($data)',
        		'headerHtmlOptions' => array('width'=>'85', ), 
        		'type' =>'raw',
        		'filter'=>CHtml::listData(array(array('id'=>1, 'name'=>'активные')), 'id', 'name'),        			
        	) 
        ) 
		,Fuels::columns(array(
			'header' => 'Остаток, л|{FuelName}',
/*			'headerHtmlOptions' => array('width'=>'50', ),				*/
			'headerHtmlOptions' => array('width'=>'65', ),				
			'value' => 'columnBalance($data, {FuelId})',			
		))			
	),
	'summaryCssClass' => 'grid-view-toolbar',
	'summaryText'=>
		'{start}-{end} из {count}' .
		CHtml::link('xls', array('excel',)),
));

?>
