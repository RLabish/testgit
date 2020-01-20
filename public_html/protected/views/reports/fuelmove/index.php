<?php 
$this->pageTitle = 'Движение ГСМ';
$filter = 'fuelmove/filter'; 
   
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
 $provider = new CActiveDataProvider($model, array('criteria'=>$criteria, 'pagination' => false,));
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

<div class="grid-view" id="fuelmove-grid">
  <table class="items">
   <thead>
	<tr>
		<th id="fuelmove-grid_c0" rowspan="2">Дата</th>
		<th id="fuelmove-grid_c6" rowspan="2">Резервуар</th>
		<th id="fuelmove-grid_c7" rowspan="2">Вид топлива</th>		
		<th id="fuelmove-grid_c2" colspan="2">Прием</th>
		<th id="fuelmove-grid_c3" colspan="2">Перемещение</th>
		<th id="fuelmove-grid_c4" rowspan="2">Расход,л</th>
		<th id="fuelmove-grid_c5" rowspan="2">Техпролив,л</th>
		<th id="fuelmove-grid_c6" rowspan="2">Остаток,л</th>
	</tr>
	<tr>
		<th rowspan="1" id="fuelmove-grid_c16">Поставщик</th>
  		<th rowspan="1" id="fuelmove-grid_c17">Объем,л</th>
 		<th rowspan="1"  id="fuelmove-grid_c18">Резервуар</th>
  		<th rowspan="1"  id="fuelmove-grid_c19">Объем,л</th>
  	</tr>
   </thead>
 <tbody>   
 
 <?php 
 	$rowNo = 0;
 	foreach ($data as $row) {
		$tankMove = array();
		if ($row->tankMoveFrom() != null) $tankMove = array_merge($tankMove, $row->tankMoveFrom());
		if ($row->tankMoveTo() != null) $tankMove = array_merge($tankMove, $row->tankMoveTo());

		
 		$rowNo++;
		echo CHtml::openTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'));
		$subRowCount = max(count($row->tankIncome), count($tankMove), 1);
		echo CHtml::tag('td', array('rowspan' => $subRowCount), Yii::app()->DateFormatter->formatDateTime($row->date,'medium',null));
		echo CHtml::tag('td', array('rowspan' => $subRowCount), $row->tank->fullName('azs,name'));
		echo CHtml::tag('td', array('rowspan' => $subRowCount), $row->tank->fuel->name);
		
		//		echo CHtml::tag('td', array('rowspan' => $subRowCount), $row->tank->terminal->name);
		if (count($row->tankIncome) > 0) {
			echo CHtml::tag('td', array(), $row->tankIncome[0]->supplier->name);
			echo CHtml::tag('td', array('class' => 'number-cell'), sprintf('%5.0f',$row->tankIncome[0]->volume));			
 		}
 		else {
 			echo CHtml::tag('td');
 			echo CHtml::tag('td');
 		}
 		
 		
 		if (count($tankMove) > 0) {
 			if (isset($tankMove[0]->tankTo) && ($tankMove[0]->tankTo->id == $row->tankId)) {
				echo CHtml::tag('td', array(), $tankMove[0]->tankFrom->fullName('azs,name'));
				echo CHtml::tag('td', array('class' => 'number-cell'), sprintf('%5.0f',$tankMove[0]->volume));
 			}	
 			else if ($tankMove[0]->tankFrom->id == $row->tankId) {
					if (isset($tankMove[0]->tankTo))
						echo CHtml::tag('td', array(), $tankMove[0]->tankTo->fullName('azs,name'));
					else
						echo CHtml::tag('td', array(), '---');
					
 					echo CHtml::tag('td', array('class' => 'number-cell'), sprintf('%5.0f',-$tankMove[0]->volume));
 			}
 			else {
 				echo CHtml::tag('td');
 				echo CHtml::tag('td'); 				
 			} 				
 		}
 		
 		
 		else {
 			echo CHtml::tag('td');
 			echo CHtml::tag('td');
 		}
 		
 		echo CHtml::tag('td', array('class' => 'number-cell', 'rowspan' => $subRowCount), sprintf('%5.0f',$row->saleVolume));
 		echo CHtml::tag('td', array('class' => 'number-cell', 'rowspan' => $subRowCount), sprintf('%5.0f',$row->serviceVolume));
 		echo CHtml::tag('td', array('class' => 'number-cell', 'rowspan' => $subRowCount), sprintf('%5.0f',$row->fuelVolume));		
 		
 		echo CHtml::closeTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'));

 		for ($i = 1; $i < $subRowCount; $i++) { 			
			echo CHtml::openTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'));
			if ($i < count($row->tankIncome)) {
 				echo CHtml::tag('td', array(), $row->tankIncome[$i]->supplier->name);
				echo CHtml::tag('td', array('class' => 'number-cell'), sprintf('%5.0f',$row->tankIncome[$i]->volume));
			}
			else {
				echo CHtml::tag('td', array(), '');
				echo CHtml::tag('td', array(), '');
			//	echo CHtml::tag('td');				
			}
			if ($i < count($tankMove)) {
				if (isset($tankMove[$i]->tankTo) && ($tankMove[$i]->tankTo->id == $row->tankId)) {
					echo CHtml::tag('td', array(), $tankMove[$i]->tankFrom->terminal->name);
					echo CHtml::tag('td', array('class' => 'number-cell'), sprintf('%5.0f',$tankMove[$i]->volume));
				}
				else if ($tankMove[$i]->tankFrom->id == $row->tankId) {
					if (isset($tankMove[$i]->tankTo)) 
						echo CHtml::tag('td', array(), $tankMove[$i]->tankTo->terminal->name);
					else
						echo CHtml::tag('td', array(), '---');
					echo CHtml::tag('td', array('class' => 'number-cell'), sprintf('%5.0f',-$tankMove[$i]->volume));
				}
				else {
					echo CHtml::tag('td', array(), '');
					echo CHtml::tag('td', array(), '');
			//		echo CHtml::tag('td');
				}
			}
			else {
				echo CHtml::tag('td', array(), '');
				echo CHtml::tag('td', array(), '');
			//	echo CHtml::tag('td');
			}
 			echo CHtml::closeTag('tr', array('class'=>($rowNo%2) ? 'odd' : 'even'));
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
