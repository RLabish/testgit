<?php $this->pageTitle = 'Системный журнал'; ?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
</div>

<?php 
$arr = date_parse(date(DATE_RSS));
$year = $arr['year'];

$this->widget('application.extensions.MyGridView', array(
	'id'=>'syslog-grid',
	'dataProvider'=> $model->search(),
	'filter'=>$model,	
	'columns'=>array(
        array(
	            'class'=>'application.extensions.SYDateColumn',
        		'name'=>'date',
				'value'=>'Yii::app()->DateFormatter->format(\'dd.MM.yy HH:mm\',$data->date)',
				'type' =>'raw',
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
//        		'cssClassExpression' => 'cellCss($data)',
				'headerHtmlOptions' => array('width'=>'100', ),
        ),	
		
//        array(
//        	'name'=>'date',
//        	'headerHtmlOptions' => array('width'=>'100', ),
//        ),	    
        array(  
        	'name'=>'user',
        	'headerHtmlOptions' => array('width'=>'80', ),
        ),
        array(
        	'name' => 'message',
		),
	),		
)); ?>
