<?php $this->pageTitle = 'Счетчики ТРК'; ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>

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

 <?php
 $data = $model->findall($criteria);
 if (count($data) == 0) {
 	echo '<span class="empty">Нет данных</span>';
 	return;
 } 
 ?>
 
 <div class="grid-view" id="counter-grid">
  <table class="items">
   <thead>
	<tr>
		<th id="counter-grid_c1">АЗС</th>
		<th id="counter-grid_c2">Терминал</th>
		<th id="counter-grid_c3">Дата</th>		
		<th id="counter-grid_c4">ТРК</th>
		<th id="counter-grid_c5">Вид топлива</th>
		<th id="counter-grid_c6">Счетчик,л</th>
	</tr>
   </thead>
 <tbody> 
 
 <?php 
 $rowNo = 0;
 foreach ($data as $azs) {
 	$termCount = count($azs->terminals);
 	$pumpCount = 0;
 	foreach($azs->terminals as $term)
 		$pumpCount += count($term->pumps);
 	
 	$rowNo++;
 	$trIsOpen = true;
 	echo CHtml::openTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'))."\n";
	echo CHtml::tag('td', array('class' => 'sum-cell', 'rowspan' => $pumpCount), $azs->name)."\n";
	foreach($azs->terminals as $term) {
		if (count($term->pumps) == 0)
			continue;
		if (!$trIsOpen) {
			$trIsOpen = true;
			echo CHtml::openTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'))."\n";
		} 			
		echo CHtml::tag('td', array('rowspan' => count($term->pumps)) , $term->name)."\n";
		echo CHtml::tag('td', array('rowspan' => count($term->pumps)) ,	Yii::app()->DateFormatter->format('dd.MM.yy HH:mm',$term->syncDate))."\n";
 			
		foreach ($term->pumps as $pump) {
			if (!$trIsOpen) {
				$trIsOpen = false;
				echo CHtml::openTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'))."\n";
			} 				
			echo CHtml::tag('td', array() , $pump->pumpNo.'-'.$pump->nozzleNo)."\n";
			echo CHtml::tag('td', array() , $pump->tank->fuel->name)."\n";		
			echo CHtml::tag('td', array('class' => 'number-cell') , number_format($pump->counter, 2, '.', ' '))."\n";
			echo CHtml::closeTag('tr')."\n"; 				
			$trIsOpen = false;
		}
	}
 	if (!$trIsOpen) 
		echo CHtml::closeTag('tr')."\n";
 }

 ?>
 
  </tbody>
 </table>

 </div>
 
 <?php 
	$baseUrl = Yii::app()->baseUrl;
	$cs = Yii::app()->getClientScript();
	$cs->registerCssFile($baseUrl.'/css/gridview.css');
	$cs->registerCssFile($baseUrl.'/css/report.css');	
?>

 
 