<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../../common/lib/yii/yii.php';
require_once($yii);

$frontendConfig=require(dirname(__FILE__).'/../config/main.php');
$commonConfig=require(dirname(__FILE__).'/../../common/config/main.php');
$config=CMap::mergeArray($frontendConfig, $commonConfig);

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

Yii::createWebApplication($config)->run();
