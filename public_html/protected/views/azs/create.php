<?php $this->pageTitle = 'Новая АЗС'; ?>

<div class="header">
	<h1><?php echo $this->pageTitle; ?></h1>
</div>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>