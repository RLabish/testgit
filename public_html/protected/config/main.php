<?php
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'iАЗС',
    'language' => 'ru',
	'theme'=>'classic', 
	
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool

		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'1',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			 'ipFilters'=>array('10.0.0.7','::1'),
		),

	),

	// application components
	'components'=>array(
		'authManager' => array(
    		'class' => 'CDbAuthManager',
			'itemTable'=> 'authitem',
			'itemChildTable'=> 'authitemchild',
			'assignmentTable'=> 'authassignment',
			'defaultRoles' => array('guest'),	
		),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),

		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=iazs',
			'emulatePrepare' => true,
			'username' => 'test',
			'password' => '1',
			'charset' => 'utf8',
			'tablePrefix' => '',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
	'log'=>array(
		'class'=>'CLogRouter',
		'routes'=>array(
			array(
				'class'=>'CFileLogRoute',
				'levels'=>'trace, info, error, warning',
			),
			// uncomment the following to show log messages on web pages
			/*
			array(
				'class'=>'CWebLogRoute',
				'levels'=>'trace, info, error, warning',
			),
			*/
			
		),
	),
		
	'widgetFactory'=>array(
		'class'=>'CWidgetFactory',
			'widgets'=>array(
			'CJuiButton'=>array('theme'=>false, 'themeUrl'=>'css'),
			'CJuiDatePicker'=>array('theme'=>false, 'themeUrl'=>'css', 'language'=>'ru'),
			'CJuiAutoComplete'=>array('theme'=>false, 'themeUrl'=>'css'),
			),
		),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);