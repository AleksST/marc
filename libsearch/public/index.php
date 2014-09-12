<?php

$yii = dirname(__FILE__) . '/../../yii/framework/yii.php';
if (is_file($loc = dirname(__FILE__) . '/../protected/config/main.loc.php')) {
	$config = $loc;
	defined('YII_DEBUG') or define('YII_DEBUG',true);
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
} else {
	$config = dirname(__FILE__) . '/../protected/config/main.php';
}

require_once($yii);
Yii::createWebApplication($config)->run();
