<?php
//	define('DOCROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
	define('DOCROOT', __DIR__ . '/');
	define('APPPATH', 'application' . '/');
	define('EXT', '.php');
	define('SEPARATOR', DIRECTORY_SEPARATOR);
	error_reporting(E_ALL);

	include_once DOCROOT . 'includes/functions.php';
	include_once DOCROOT . 'includes/routes.php';

	try {
		echo Routes::detect();
	}
	catch (Route_Exception $e) {
		echo $e->debug(false);
	}