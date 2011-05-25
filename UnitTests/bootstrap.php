<?php
use MySQLi_Classes\Connector;
require_once 'MySQLi_Classes/autoloader.inc.php';
$properties = parse_ini_file('build.properties');
$GLOBALS['mysqli'] = new MySQLi(
	$properties['mysql.hostname'],
	$properties['mysql.username'],
	$properties['mysql.password'],
	$properties['test.schema'],
	$properties['mysql.port']);
$GLOBALS['mysqli']->set_charset("utf8");
$GLOBALS['schema'] = $properties['test.schema'];
$GLOBALS['comparisonSchema'] = $properties['test.schema.comparison'];
Connector::connect($GLOBALS['mysqli']);
require_once __DIR__.'/TableComparisonTestCase.class.php';