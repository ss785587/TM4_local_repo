<?php
# Setting up the frontend-specific aliases
define('ROOT_DIR', realpath(__DIR__ . '/../..'));
define('REL_PATH_TESTENGINE', './../../testengine/www');
define('REL_PATH_FRONTEND', './../../frontend/www');
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');
Yii::setPathOfAlias('root', ROOT_DIR);
Yii::setPathOfAlias('common', ROOT_DIR . DIRECTORY_SEPARATOR . 'common');


// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	// application components
	'components'=>array(
	
		// MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=tm4',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'info@tm4.de',
	),
);