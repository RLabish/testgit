<?php 
$this->pageTitle = 'Сменный отчет';
$filter = 'shift/filter';
$filterData = array('dateFrom'=>$dateFrom,'dateTo'=>$dateTo,'azsId' => $azsId,);

   
if (!isset($_GET['submit'])) {
?>
	<h1><?php echo $this->pageTitle; ?></h1>
	<?php
	$this->renderPartial($filter, $filterData);
	return;	
}
?>

<?php 	$this->renderPartial($filter, $filterData); ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>

 <?php 
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

<div class="grid-view" id="shift-grid">
  <table class="items">
   <thead>
	<tr>
		<th id="shift-grid_c1" rowspan="2">Вид топлива</th>
		<th id="shift-grid_c2" rowspan="2">Резервуар</th>
		<th id="shift-grid_c3" colspan="2">На начало</th>		
		<th id="shift-grid_c4" colspan="2">Принято</th>
		<th id="shift-grid_c5" colspan="2">Выдано</th>
		<th id="shift-grid_c6" rowspan="2">Тех пролив</th>
		<th id="shift-grid_c7" colspan="2">Остаток</th>
		<th id="shift-grid_c8" rowspan="2">Разница</th>
	</tr>
	<tr>
		<th rowspan="1" id="shift-grid_c21">Факт</th>		
		<th rowspan="1" id="shift-grid_c22">Книж</th>		
		<th rowspan="1" id="shift-grid_c31">ТТН</th>
  		<th rowspan="1" id="shift-grid_c32">перемещ</th>
 		<th rowspan="1"  id="shift-grid_c51">ТРК</th>
  		<th rowspan="1"  id="shift-grid_c52">перемещ</th>
		<th rowspan="1" id="shift-grid_c71">Факт</th>		
		<th rowspan="1" id="shift-grid_c72">Книж</th>		  		
	</tr>
   </thead>
 <tbody>   
 
 <?php 
	function volumeIsDefined($volume) {
		return (gettype($volume) != 'string') || ($volume != '-');
	}
	
 	function printVolumeCell($volume, $isSum = false, $errIfNeg = false, $hideZero = true) {
		if (!volumeIsDefined($volume)) {
			echo CHtml::tag('td', array('class' => 'number-cell error-cell') , 'н/д');
			return;
		}
 		$opt = 'number-cell';
 		if ($isSum)
 			$opt = $opt.' sum-cell';
 		if ($errIfNeg && ($volume < 0))
 			$opt = $opt.' error-cell'; 		
		if ($isSum) 
			$hideZero = false;
 		if ($hideZero && ($volume == 0))			 			
 			echo CHtml::tag('td', array() , ' ');
 		else 		
 			echo CHtml::tag('td', array('class' => $opt), number_format ($volume, 0, '.', ' '));
 	}
 	$i = 0;
 	foreach ($data as $fuel) {
 		$i++;
		echo CHtml::openTag('tr', array('class'=>($i%2) ? 'odd' : 'even'));
		$subRowCount = count($fuel['tanks']);
		if ($subRowCount > 1)
			echo CHtml::tag('td', array('class' => 'sum-cell', 'rowspan' => $subRowCount + 1), $fuel['info']);
		else
			echo CHtml::tag('td', array('class' => 'sum-cell'), $fuel['info']);
		$sumBeginRealVolume = 0;
		$sumBeginBookVolume = 0;
		$sumIncomeVolume = 0;
		$sumSaleVolume = 0;
		$sumServiceVolume = 0;
		$sumMoveInVolume = 0;
		$sumMoveOutVolume = 0;
		$sumEndRealVolume = 0;
		$sumEndBookVolume = 0;		
		$j = 0;			
 		foreach ($fuel['tanks'] as $tank) {
 			echo CHtml::tag('td', array('class' => 'sum-cell'), $tank['info']);
 			printVolumeCell($tank['beginRealVolume'], $subRowCount == 1, true, false );
 			printVolumeCell($tank['beginBookVolume'], $subRowCount == 1, true, false);
 			printVolumeCell($tank['incomeVolume'], $subRowCount == 1);
 			printVolumeCell($tank['moveInVolume'], $subRowCount == 1);
 			printVolumeCell($tank['saleVolume'], $subRowCount == 1);
 			printVolumeCell($tank['moveOutVolume'], $subRowCount == 1);
 			printVolumeCell($tank['serviceVolume'], $subRowCount == 1);
 			printVolumeCell($tank['endRealVolume'], $subRowCount == 1, true, false);
 			printVolumeCell($tank['endBookVolume'], $subRowCount == 1, true, false);
			if (volumeIsDefined($tank['endRealVolume']) && volumeIsDefined($tank['endBookVolume']))
				printVolumeCell($tank['endRealVolume'] - $tank['endBookVolume'], $subRowCount == 1, true, false);
 			else
				printVolumeCell('-', $subRowCount == 1, true);
 			
			if ((volumeIsDefined($sumBeginRealVolume)) && (volumeIsDefined($tank['beginRealVolume'])))
				$sumBeginRealVolume += $tank['beginRealVolume'];
 			else
				$sumBeginRealVolume = '-';
				
			$sumBeginBookVolume += $tank['beginBookVolume'];
 			$sumIncomeVolume += $tank['incomeVolume'];
 			$sumMoveInVolume += $tank['moveInVolume'];
 			$sumSaleVolume += $tank['saleVolume'];
 			$sumMoveOutVolume += $tank['moveOutVolume'];
 			$sumServiceVolume += $tank['serviceVolume']; 	
			if ((volumeIsDefined($sumEndRealVolume)) && (volumeIsDefined($tank['endRealVolume'])))
				$sumEndRealVolume += $tank['endRealVolume'];
			else
				$sumEndRealVolume = '-';
			$sumEndBookVolume += $tank['endBookVolume'];
 			
 			echo CHtml::closeTag('tr');
			if ($j++ < $subRowCount - 1)
				echo CHtml::openTag('tr', array('class'=>($i%2) ? 'odd' : 'even')); 
 		}
 		echo CHtml::closeTag('tr');
 		if ($subRowCount > 1) { 			
			echo CHtml::openTag('tr', array('class'=>($i%2) ? 'odd' : 'even')); 			
 			echo CHtml::tag('td', array('class' => 'sum-cell'), 'ИТОГО');
 			printVolumeCell($sumBeginRealVolume, true, true, false);
 			printVolumeCell($sumBeginBookVolume, true, true, false);
 			printVolumeCell($sumIncomeVolume, true);
 			printVolumeCell($sumMoveInVolume, true);
 			printVolumeCell($sumSaleVolume, true);
 			printVolumeCell($sumMoveOutVolume, true);
 			printVolumeCell($sumServiceVolume, true);
 			printVolumeCell($sumEndRealVolume, true, true, false);
 			printVolumeCell($sumEndBookVolume, true, true, false); 	
			
			if ((volumeIsDefined($sumEndRealVolume)) && (volumeIsDefined($sumEndBookVolume)))
				printVolumeCell($sumEndRealVolume - $sumEndBookVolume, true, true);
			else
				printVolumeCell('-', true, true);
 			
 			echo CHtml::closeTag('tr'); 		
 		}
 	}
 	echo CHtml::closeTag('tr'); 
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



