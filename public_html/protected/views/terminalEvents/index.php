<?php $this->pageTitle = 'Журнал событий терминала'; ?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
</div>

<div class="toolbar grid-toolbar">
	<?php $this->widget('zii.widgets.jui.CJuiButton',
			array(
					'name'=>'btn-xls',
					'buttonType'=>'button',
					'htmlOptions'=>array('class'=>'tb-btn xls-tb-btn', 'title'=>'excel'),
					'onclick'=>'js:function(){gridtoexcel("terminalevents-grid","'.$this->createUrl('excel').'");}',
			)
	);
	?>
</div>

<?php 
$arr = date_parse(date(DATE_RSS));
$year = $arr['year'];

$this->widget('application.extensions.MyGridView', array(
	'id'=>'terminalevents-grid',
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
        array(  
        	'header'=>'Терминал',    
	    	'name'=>'terminalId',	  
	    	'value'=>'$data->terminal->name',	        	
        	'filter'=>CHtml::listData(Terminals::model()->findall(array('order' => 'name',)), 'id', 'name'),			
        	'headerHtmlOptions' => array('width'=>'80', ),
        ),
        array(
        	'name' => 'msg',
		),
	),		
)); ?>
