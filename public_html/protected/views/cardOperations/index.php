<?php $this->pageTitle = 'Журнал операций'; ?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
</div>

<div class="toolbar grid-toolbar">
	<?php $this->widget('zii.widgets.jui.CJuiButton',
			array(
					'name'=>'btn-xls',
					'buttonType'=>'button',
					'htmlOptions'=>array('class'=>'tb-btn xls-tb-btn', 'title'=>'excel'),
					'onclick'=>'js:function(){gridtoexcel("operations-grid","'.$this->createUrl('excel').'");}',
			)
	);
	?>
</div>

<?php 
function cellCss($model) {
	if ($model->state != 0)
		return 'oper-active';
	else if ($model->operationType == CardOperations::TYPE_PUMP_ALARM)
		return 'oper-autonom';
	else if ($model->operationType == CardOperations::TYPE_CARD_REFILL)
		return 'oper-refill';
	else if ($model->operationType == CardOperations::TYPE_PUMP_SERVICE)
		return 'oper-service';
	else if ($model->operationType == CardOperations::TYPE_PUMP_MOVE)
		return 'oper-move';
	return '';
}


function columnNumber($row) {
	if ($row->card->id > 0) 
		return	'<a href="'.Yii::app()->createUrl("cards/view",array("id"=>$row->card->id)).'" title="Регистрационная информация">'.$row->card->number.'</a>';	
	else
		return $row->card->number;
}


$arr = date_parse(date(DATE_RSS));
$year = $arr['year'];

$operation_filter_arr = array();
array_push($operation_filter_arr, array('id'=>1, 'name'=>'пополнение карты'));
array_push($operation_filter_arr, array('id'=>2, 'name'=>'выдача топлива'));
$tm = Terminals::model()->findall();
foreach ($tm as $i) 
	array_push($operation_filter_arr, array('id'=>'2.'.$i->id, 'name'=>$i->name));
array_push($operation_filter_arr, array('id'=>5, 'name'=>'перемещение'));
array_push($operation_filter_arr, array('id'=>4, 'name'=>'техпролив'));
array_push($operation_filter_arr, array('id'=>3, 'name'=>'автономный пролив'));
			
$this->widget('application.extensions.MyGridView', array(
	'id'=>'operations-grid',
	'dataProvider'=> $model->search(),
	'filter'=>$model,
	'columns'=>array(
        array(
	            'class'=>'application.extensions.SYDateColumn',
        		'name'=>'date',
        		'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yy HH:mm\',$data->date)',
        		'fromText'=>'c',
	            'toText'=>'по',
        		'dateLabelStyle'=>'width:20%;display:block;float:left;margin-bottom: 3px;',//width:25%;display:block;float:left;',  
        		'dateInputStyle'=>'width:70%',
        		
                'dateFormat'=>'dd.mm.y',
        		'language'=>'ru',
      			'dateOptions'=>array(
          			'showAnim'=>'fold',
					'dateFormat'=>'dd.mm.yy',
					'changeYear'=> true,
                	'showButtonPanel' => true,
					'yearRange' => ($year - 2).':'.$year,
				), 
        		'cssClassExpression' => 'cellCss($data)',
        ),	    
        array(        
        	'name'=>'cardNumber',
        	'value'=>'columnNumber($data)',
        	'header' => 'Карта|№',
        	'cssClassExpression' => 'cellCss($data)',
        	'type' =>'raw',
		),
        array(
        	'name' => 'cardOwner',
        	'header' => 'Карта|Владелец',
        	'value' => '$data->card->owner',
        	'cssClassExpression' => 'cellCss($data)',        		
	),
	array(
		'name'=>'operationType',
		'value' => '$data->description',
		'filter'=>CHtml::listData($operation_filter_arr, 'id', 'name'),				
        	'cssClassExpression' => 'cellCss($data)',        		
	),	
    array(
	    	'name'=>'fuelId',	        	
	    	'value'=>'$data->fuel->name',	        	
        	'header' => 'Продукт',
        	'filter'=>CHtml::listData(Fuels::model()->findall(array('order' => 'name',)), 'id', 'name'),
        	'cssClassExpression' => 'cellCss($data)',        		
	),		
	array(
	    	'name'=>'volume',	        	
			'htmlOptions' =>  array('class'=>"number-cell"),
			'cssClassExpression' => 'cellCss($data)',				
	),
	array(
	    	'name'=>'balance',	        	
			'htmlOptions' =>  array('class'=>"number-cell"),
			'cssClassExpression' => 'cellCss($data)',				
	),	
),
)); ?>
