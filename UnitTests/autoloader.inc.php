<?php
spl_autoload_register(function($className) {
	$file = str_replace('\\', DIRECTORY_SEPARATOR, $className);
	$includePaths = explode(PATH_SEPARATOR, get_include_path());
	foreach($includePaths as $includePath) {
		$includePath = realpath($includePath);
		if(file_exists($includePath.DIRECTORY_SEPARATOR.$file.'.class.php')) {
			require_once($file.'.class.php');
			return true;
		}
		if(file_exists($includePath.DIRECTORY_SEPARATOR.$file.'.interface.php')) {
			require_once($file.'.interface.php');
			return true;
		}
	}
	return false;
});