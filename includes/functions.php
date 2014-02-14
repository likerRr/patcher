<?php defined('DOCROOT') or die('Denied direct script access');
	/**
	 * Auto Load Classes
	 * @param $class
	 */
	function __autoload($class) {
		$class = str_replace('_', '/', $class);
		include DOCROOT . 'classes/' . $class . EXT;
	}