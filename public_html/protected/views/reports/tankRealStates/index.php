<?php 
$this->pageTitle = 'Хронология состояния резервуаров';
$filter = 'tankRealStates/filter'; 
   
if (!isset($_GET['submit'])) {
?>
	<h1><?php echo $this->pageTitle; ?></h1>
	<?php
	$this->renderPartial($filter, array('model'=>$model));
	return;	
}
?>

<?php $this->renderPartial($filter, array('model'=>$model));  ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>

 <?php 
 $provider = $model->search();
 $provider->pagination = false;  
 $data = $provider->getData(); 
 if (count($data) == 0) { 
	echo '<span class="empty">Нет данных</span>';
	return;
 } 
 ?>

 <div class="toolbar grid-toolbar">
	<?php $this->widget('zii.widgets.jui.CJuiButton',
				array(
						'name'=>'btn-xls',
						'buttonType'=>'button',
						'htmlOptions'=>array('class'=>'tb-btn xls-tb-btn', 'title'=>'excel'),						
						'onclick'=>
						'js:function(){'
						.'location.href = \''.Yii::app()->request->requestUri.'&view=excel'.'\';'
						.'return false;'
						.'}'

				)
		);
	?>
</div>

<div id="tankRealStates-chart"></div>

 <?php
 $ser1 = '';
  
 $mark_time = 0;
 $mark_level = 0;
 $mark_volume = 0;

 $prev_time = 0;
 $prev_level = 0;
 $prev_volume = 0;

 $cur_time = 0;
 $cur_level = 0;
 $cur_volume = 0;
 
 $saved = False;

 foreach ($data as $item) {
	if ($item->status != 0)
		continue;
	$saved = False;
	$prev_time = $cur_time;
	$prev_level = $cur_level;
	$prev_volume = $cur_volume;
	$cur_time = strtotime($item->date)*1000;
	$cur_volume = $item->fuelVolume;
	$cur_level = $item->fuelLevel;
	
	if (($mark_time == 0) || (abs($cur_level - $mark_level) > 1.5)) {
		if ($mark_time == 0)
			$ser1 = '['.$cur_time.', '.$cur_volume.']'; 
		else if ($prev_time == $cur_time)
			$ser1 = $ser1.'['.$cur_time.', '.$cur_volume.']'; 
		else		
			$ser1 = $ser1.',['.$prev_time.', '.$prev_volume.'],['.$cur_time.', '.$cur_volume.']'; 
		$saved = True;
		$mark_time = $cur_time;
		$mark_level = $cur_level;
		$mark_volume = $cur_volume;
	}
}
if (!$saved) 
	$ser1 = $ser1.',['.$cur_time.', '.$cur_volume.']'; 
//$ser1 = substr($ser1, 1);
 

 $js=<<<EOD
 $(function () {
 		Highcharts.setOptions({
			global: {
				useUTC: false
				}
		});
 
        $('#tankRealStates-chart').highcharts({
            chart: {
                type: 'spline'
            },
            
            title: {
                text: ''
            },
            /*
            subtitle: {
                text: 'subtitle'
            },
            */
			
            xAxis: {
                type: 'datetime',
                labels: {
					format: '{value:%d.%m.%y %H:%M}',
					rotation: -90,
					align: 'right'
        		}
            },
            yAxis: {
                title: {
                    text: 'Объем, л'
                },
                min: 0
            },
            tooltip: {
                formatter: function() {
                	return Highcharts.dateFormat('%d.%m.%y %H:%M', this.x) +'<br/> <b>'+ this.y +' л</b>';
				}
            },
            legend: {
            	enabled: false
            },            
            series: [{
                name: 'name',
                // Define the data points. All series have a dummy year
                // of 1970/71 in order to be compared on the same x axis. Note
                // that in JavaScript, months start at 0 for January, 1 for February etc.
                data: [ $ser1
                ]
            }]
        });
    });
EOD;

 
 $baseUrl = Yii::app()->baseUrl;
 $cs=Yii::app()->getClientScript();
 $cs->registerScript(__CLASS__, $js);
 $cs->registerScriptFile($baseUrl.'/js/highcharts/highcharts.js');
 $cs->registerCssFile($baseUrl.'/css/gridview.css');
 $cs->registerCssFile($baseUrl.'/css/report.css');
