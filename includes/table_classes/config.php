<?php
	//define database configuration values
	$options = &PEAR::getStaticProperty('DB_DataObject','options');

	$options = array(
	'database' => 'mysql://' . DB_USER . ':'. DB_PASS .'@' . DB_HOST . '/' . DB_NAME,
	'schema_location' => INCLUDE_DIR . '/table_classes/',
	'class_location' => INCLUDE_DIR . '/table_classes/',
	'require_prefix' => 'DataObjects/',
	'class_prefix' => 'tableclass_',
	'db_driver' => 'MDB2',
		'ini_' . DB_NAME => INCLUDE_DIR . '/table_classes/groups.ini',
	"debug" => SQL_DEBUG_LEVEL
	);
?>
