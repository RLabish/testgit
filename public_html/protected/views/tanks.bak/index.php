<?php 
 $this->pageTitle = 'Резервуары'; 	 
 $cssUrl = Yii::app()->baseUrl.'/css/';
 $cssFile = $cssUrl.'gridview.css';
 Yii::app()->clientScript->registerCssFile($cssFile);
 
?>

<div class="header">
	<h1><?php echo $this->pageTitle ?></h1>
</div>

<div class="grid-toolbar">
	<?php /*$this->widget('zii.widgets.jui.CJuiButton',
			array(
				'name'=>'btn-xls',
				'buttonType'=>'button',
				'htmlOptions'=>array('class'=>'tb-btn xls-tb-btn', 'title'=>'excel'),
				'onclick'=>'js:function(){gridtoexcel("card-operations-grid","'.CardOperationsController::createUrl('excel').'");}',
    		)						
		);
*/	?>
</div>

<div id="tank-state-view" class="grid-view">

<?php
 $azsName = ''; 
 foreach ($items as $item) {
 	if ($azsName != $item->azs->name) {
 		$azsName = $item->azs->name;
 		if (isset($_GET['bydep'])) 
 			echo CHtml::link($azsName, Yii::app()->createUrl("tanks/index",array("depId"=>$item->azs->departament->id)), array());
 		else
 			echo CHtml::tag('h2', array(), $azsName);
 			
 	}
 	$this->renderPartial('_view',array('data'=>$item));
 }
?>

</div>
