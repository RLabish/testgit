<?php 
 $user = Users::currentUser();
 if (!$user | !$user->role) Yii::app()->user->logout();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/menu.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tank-view.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mymain.css" />

	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/iazs.js"></script>
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php Yii::app()->getClientScript()->registerCoreScript('jquery');?> 

<script type="text/javascript">
	function log(text) {
		$('#console').append('<div>' + text + '</div>');
	}
	$(function(){
		$('#nav li')
			.bind('mouseover', function (event){$(this).addClass('over');})
			.bind('mouseout', function (event) {$(this).removeClass('over');});
	});	
</script>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div class="nav-bar">
		<?php		
			$this->widget('zii.widgets.CMenu',array(
				'id'=>'nav',
				'activateParents'=>true,
				'items'=>array(
					array('label'=>'Карты',
						'url'=>array('/cards/index'),
					),					
					array('label'=>'Операции', 'url'=>array('/CardOperations/index')),
					array('label'=>'Резервуары',
								'url'=>'javascript:void(0);',
								'itemOptions'=>array('class'=>'parent'),
								'items'=>array(
									array('label'=>'Состояние резервуаров', 'url'=>array('tanks/index')),
									array('label'=>'Прием топлива', 'url'=>array('tankIncome/index')),
									array('label'=>'Перемещение', 'url'=>array('tankMove/index')),
									array('label'=>'Инвентаризация', 'url'=>array('tankInventory/index')),
									array('label'=>'Фактические остатки', 'url'=>array('tanks/realStates')),									
								),
					),					
					array('label'=>'Справочники',
								'url'=>'javascript:void(0);',
								'itemOptions'=>array('class'=>'parent'),
								'items'=>array(
									array('label'=>'Поставщики', 'url'=>array('suppliers/index')),
								),
					),					
						
					array('label'=>'Отчеты',
								'url'=>'javascript:void(0);',
								'itemOptions'=>array('class'=>'parent'),
								'items'=>array(
									array('label'=>'Заправочная ведомость', 'url'=>array('reports/sales/index')),
									array('label'=>'Сводный отчет по реализации', 'url'=>array('reports/sumsales/index')),
									array('label'=>'Движение ГСМ', 'url'=>array('reports/fuelmove/index')),
									array('label'=>'Сменный отчет', 'url'=>array('reports/shift/index')),									
									array('label'=>'Хронология состояния резервуаров', 'url'=>array('reports/tankRealStates/index')),	
								),
					),					
					array('label'=>'Сервис',
								'url'=>'javascript:void(0);',
								'itemOptions'=>array('class'=>'parent'),
								'items'=>array(
                                    array('label'=>'Терминалы', 'url'=>array('reports/terminals')),								
									array('label'=>'Счетчики ТРК', 'url'=>array('reports/counters')),
									array('label'=>'Журнал событий терминала', 'url'=>array('terminalEvents/index')),																		
								),
					),					
					array(
						'label'=>'Администрирование',
						'visible' => Yii::app()->user->checkAccess('admin'),
						'url'=>'javascript:void(0);',
						'itemOptions'=>array('class'=>'parent'),
						'items'=>array(
							array('label'=>'Виды топлива', 'url'=>array('Fuels/index'), 'visible' => Yii::app()->user->checkAccess('config')),
							array('label'=>'АЗС', 'url'=>array('azs/index'), 'visible' => Yii::app()->user->checkAccess('config')),
							array('label'=>'Терминалы', 'url'=>array('terminals/index'), 'visible' => Yii::app()->user->checkAccess('config')),
							array('label'=>'Резервуары', 'url'=>array('tanks/admin'), 'visible' => Yii::app()->user->checkAccess('config')),
							array('label'=>'ТРК', 'url'=>array('pumps/index'), 'visible' => Yii::app()->user->checkAccess('config')),
							array('label'=>'Пользователи', 'url'=>array('users/index')),
							array('label'=>'Системный журнал', 'url'=>array('syslog/index')),																	
						),
					),				
			),
		)); ?>
	</div><!-- mainmenu -->

	  <?php	if (!Yii::app()->user->isGuest) {  ?>
	  	<div id='right-menu'>
			<p>Пользователь</p><?php echo CHtml::link(Yii::app()->user->name, array('Site/cabinet',)) ?>
			<p>|</p>
			<?php echo CHtml::link('Выход', array('Site/logout',)) ?>
		</div>
	  <?php }?>		
	
	<?php
        if (isset(Yii::app()->params['message']) && Yii::app()->params['message']['visible']) {
            echo '<div id="notice">';
            $msg = Yii::app()->params['message'];
            echo $msg['html'];
            echo '</div>';
        }
    ?>
		
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

<?php /*	
	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->
*/ ?>
	
</div><!-- page -->

<?php /*
<div class="console" id="console">
============ CONSOLE ================
</div>
*/ ?>


</body>
</html>
