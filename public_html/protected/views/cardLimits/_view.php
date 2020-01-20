<?php
/* @var $this CardLimitsController */
/* @var $data CardLimits */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('card_id')); ?>:</b>
	<?php echo CHtml::encode($data->card_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('limit_type')); ?>:</b>
	<?php echo CHtml::encode($data->limit_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fuel_id')); ?>:</b>
	<?php echo CHtml::encode($data->fuel_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_volume')); ?>:</b>
	<?php echo CHtml::encode($data->order_volume); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('usage_volume')); ?>:</b>
	<?php echo CHtml::encode($data->usage_volume); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_sale_date')); ?>:</b>
	<?php echo CHtml::encode($data->last_sale_date); ?>
	<br />


</div>