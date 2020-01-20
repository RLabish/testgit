<?php $this->pageTitle = 'Пользователи'; ?>

<div class="header grid-header">
	<h1><?php echo $this->pageTitle ?></h1>
	<div class="buttons">
		<?php
		 $this->widget('zii.widgets.jui.CJuiButton',
						array(
							'name'=>'btn-auto-create',
							'buttonType'=>'button',
							'caption'=>'Новый пользователь',
							'htmlOptions'=>array('class'=>'btn-add'),
							'onclick'=>'js:function(){location.href="'.Yii::app()->createUrl("users/create").'";return false;}',
						)
				 );
  		?>
	</div>
</div>

<?php

 function columnName($data) {
 	return	'<a href="'.Yii::app()->createUrl("users/update",array("id"=>$data->id)).'" title="Изменить">'.$data->username.'</a>';
 }
 
 function columnRole($data) {    
	if ($data->role == '')
		return 'заблокирован';
	else if (isset($data->roleNames()[$data->role]))
	    return $data->roleNames()[$data->role];
    else
    	$data->role;	
 }
 
 function columnActivity($data) {    
	if (count($data->sessions) > 0)
		return 'на сайте (ip: '.$data->last_ip.')';
	else
		if (isset($data->last_activity))
			return  'был на сайте '.Yii::app()->DateFormatter->format('dd.MM.yy HH:mm',$data->last_activity).' (ip: '.$data->last_ip.')';
	
	return '';
//	return 'columnActivity';
 }
 
 function cellCss($data) {
	if ($data->role == '')
		return 'error-cell';
}

 function activityCss($data) {
	if (count($data->sessions) == 0)
		return 'unactive-cell';
	else
		return 'mark-cell';
}
 
$this->widget('application.extensions.MyGridView', array(
	'id'=>'users-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=> array_merge( 
		array(
			array(
				'name'=>'username',
				'value'=>'columnName($data)',
				'type' =>'raw',	
				'cssClassExpression' => 'cellCss($data)'			
			),
			array (
				'name'=>'fullname',			
				'cssClassExpression' => 'cellCss($data)',
			),			
			array(
				'name'=>'role',
				'value'=>'columnRole($data)',
				'type' =>'raw',				
				'cssClassExpression' => 'cellCss($data)'			
			),
			array (
				'name' => 'description',
				'cssClassExpression' => 'cellCss($data)'			
			),
		),
		(get_class(Yii::app()->session) != 'DbHttpSession') ? array() : array (
			array(
				'name'=>'last_activity',
				'header'=>'Активность',
				'value'=>'columnActivity($data)',
				'type' =>'raw',	
				'cssClassExpression' => 'activityCss($data)'			
			),
		)
	),
	'summaryCssClass' => 'grid-view-toolbar',
	'summaryText'=>
		'{start}-{end} из {count}' /* .
		CHtml::link('xls', array('excel',))*/,
));
?>
