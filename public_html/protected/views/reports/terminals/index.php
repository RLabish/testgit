<?php $this->pageTitle = 'Терминалы'; ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>

 <div class="toolbar grid-toolbar">
	<?php /*$this->widget('zii.widgets.jui.CJuiButton',
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
	*/ ?>
</div>

 <?php
 $data = $model->findall($criteria);
 if (count($data) == 0) {
 	echo '<span class="empty">Нет данных</span>';
 	return;
 } 
 ?>
 
 <div class="grid-view" id="terminals-grid">
  <table class="items">
   <thead>
	<tr>
		<th id="terminals-grid_c1">АЗС</th>
		<th id="terminals-grid_c2">Терминал</th>
		<th id="terminals-grid_c3">Синхронизация</th>		
		<th id="terminals-grid_c4">Состояние</th>
	</tr>
   </thead>
 <tbody> 
 
 <?php 
 $rowNo = 0;
 foreach ($data as $azs) {
 	$termCount = count($azs->terminals);
	if ($termCount == 0) continue;	
 	$rowNo++;
 	$trIsOpen = true;
 	echo CHtml::openTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'))."\n";
	echo CHtml::tag('td', array('class' => 'sum-cell', 'rowspan' => $termCount), $azs->name)."\n";
	foreach($azs->terminals as $term) {
		if (!$trIsOpen) {
			$trIsOpen = true;
			echo CHtml::openTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'))."\n";
		} 			
		echo CHtml::tag('td', array() , $term->name)."\n";
		echo CHtml::tag('td', array() ,	Yii::app()->DateFormatter->format('dd.MM.yy HH:mm',$term->syncDate))."\n";
		echo CHtml::tag('td', $term->isOk ? array() : array('class' => 'error-cell') ,	implode('<br>', $term->stateInfo))."\n";
		echo CHtml::closeTag('tr')."\n";
		$trIsOpen = false
		;
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

 
 