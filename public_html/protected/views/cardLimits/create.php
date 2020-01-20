<?php 
$card = Cards::model()->find(array('condition'=>'id=:id','params'=>array(':id'=>$model->cardId)));
$this->pageTitle = 'Лимит по карте'.$card->number.' /'.$card->owner.'/'; 
?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>