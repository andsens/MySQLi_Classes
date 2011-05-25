<?php
spl_autoload_register(function($className) {
	$namespace = 'MySQLi_Classes\\';
	if(strtolower(substr($className, 0, strlen($namespace))) !== strtolower($namespace))
		return false;
	$file = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, strlen($namespace)));
	if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$file.'.class.php')) {
		require_once($file.'.class.php');
		if(!class_exists($className, false))
			die('Class ' . $className . ' was not present in '.__DIR__ . DIRECTORY_SEPARATOR . $file .'")');
		return true;
	}
	if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$file.'.interface.php')) {
		require_once($file.'.interface.php');
		if(!interface_exists($className, false))
			die('Interface ' . $className . ' was not present in '.__DIR__ . DIRECTORY_SEPARATOR . $file .'")');
		return true;
	}
	return false;
});