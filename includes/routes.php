<?php defined('DOCROOT') or die('Denied direct script access');

	Routes::setAppPath(APPPATH);

	/**
	 * Main Page
	 */
	Routes::add('index', '(<action>)', array('action' => 'create|check',))
		->file('menu/handler/main');