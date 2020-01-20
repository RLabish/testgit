<?php 
$this->pageTitle = 'Сводный отчет по реализации ГСМ';
$filter = 'sumsales/filter'; 
   
if (!isset($_GET['submit'])) {
?>
	<h1><?php echo $this->pageTitle; ?></h1>
	<?php
	$this->renderPartial($filter, array('model'=>$model,'groupDefs'=>$groupDefs,'groups'=>$groups));
	return;	
}
?>

<?php $this->renderPartial($filter, array('model'=>$model,'groupDefs'=>$groupDefs,'groups'=>$groups));  ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>


<?php
 global $data;
 $data = array();
 global $fuels; 
 $fuels = Fuels::model()->findAll(array('order' => 'code'));

 global $itemId;

 function newItem() {
 	global $itemId;
 	$itemId++;
 	$item['id'] = $itemId;
 	$item['group'] = null;
 	$item['level'] = 0;
 	global $fuels;
 	foreach ($fuels as $fuel)
 		$item['fuels'][$fuel->name] = 0;
 	$item['rows'] = 0;
 	$item['childs'] = array();
 	return $item;
 }

 $data = newItem();
 foreach ($model->findAll($criteria) as $row ) {
 	$item = &$data;
 	$newItemCnt = 0;
 	$level = 0;
 	foreach ($groups as $g) {
 		$level++;
 		$item['group'] = $g;
 		$field = $groupDefs[$g]['alias']; 	
		if (!isset($item['childs'][$row->$field])) {
 			$i = newItem();
 			$newItemCnt++;
 			$i['parent'] = &$item;
 			$i['level'] = $level;
 			$item['childs'][$row->$field] = $i;
 			$item = &$item['childs'][$row->$field];
 			unset($i);
 		}
 		else 
 			$item = &$item['childs'][$row->$field];
 	}
 	
	$i = &$item; 	
	while (isset($i)) {
		if ($newItemCnt > 0) 
			$i['rows']++;
 		$i['fuels'][$row->fuelName] += $row->volume;
		$i = &$i['parent']; 		
	}
 	unset($i);
 	unset($item); 	
 }
 
 if ($data['rows'] == 0) {
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
 
 <div class="grid-view" id="sumsales-grid">
  <table class="items">
   <thead>
    <tr><?php
 		$i = 0;
 		foreach ($groups as $g) 
 			echo CHtml::tag('th', array('id'=>'sumsales-grid_g'.$i++), $groupDefs[$g]['header']);
 		$i = 0;
 		foreach ($fuels as $f)
 			echo CHtml::tag('th', array('id'=>'sumsales-grid_f'.$i++), $f->name);
 		
	 ?>
    </tr>
  </thead>
 <tbody>
 
 <?php
  global $rowOpen;
  $rowOpen = false;
  global $rowNo;
  $rowNo = 1;

  function tr($open, $close) {
  	global $rowOpen;
  	global $rowNo;  	
  	if ($open && !$rowOpen) {
  		echo CHtml::tag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'), false, false);
  		$rowOpen = true;
  	}
  	if ($close && $rowOpen) {
		echo "</tr>\n";
		$rowOpen = false;
  	}
  }
  
  function printItem($item) {
  	global $groupDefs;
  	global $fuels;
  	global $rowNo;
  	tr(true, false);
  	if (count($item['childs']) == 0) {
		$class = 'cell-number'.(($item['parent']['rows'] <= 1) ? ' cell-mark' : '');
  		foreach ($fuels as $fuel)
  			echo CHtml::tag('td', array('class'=>$class), number_format($item['fuels'][$fuel->name], 2, '.', ' '));
		tr(false, true);
  	}
  	else {  		
  		foreach ($item['childs'] as $key => $value) {
  			tr(true, false);  			
  			$hasTotal = (is_array($groupDefs)) && ($value['level'] < count($groupDefs)) && ($value['rows'] > 1);
  			echo CHtml::tag('td', array('rowspan'=>$hasTotal?$value['rows'] + 1:$value['rows']), $key);
 			printItem($value);
 			if ($hasTotal) {
 				tr(true, false);
 				echo CHtml::tag('td', array('class' => 'cell-total'), 'Итого');
 				foreach ($fuels as $fuel)
 					echo CHtml::tag('td', array('class'=>'cell-number cell-mark'), number_format($value['fuels'][$fuel->name], 2, '.', ' '));
 				tr(false, true);
 			}
 			if ($value['level'] == 1)
 				$rowNo++; 				
  		}  		
  	}
  }
  printItem($data);
  if ($data['rows'] > 1) {
  	tr(true, false);
  	echo CHtml::tag('td', array('class' => 'cell-total'), 'Итого');
  	for ($i = 1; $i < count($groups); $i++)
  		echo CHtml::tag('td');
  	foreach ($fuels as $fuel)
  		echo CHtml::tag('td', array('class'=>'cell-number cell-mark'), number_format($data['fuels'][$fuel->name], 2, '.', ' '));
  	tr(false, true);
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