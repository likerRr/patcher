<?php defined('DOCROOT') or die('Denied direct script access');

	$data        = array();
	$error       = false;
	$path        = 'd:\WebServer\OpenServer\domains\pm-app';
	$patchFolder = 'd:\WebServer\OpenServer\domains\pm-app\..\PM-APP-PATCHES\\' . date('d.m.Y');
	$date        = strtotime('28.01.2014 00:30:00');
	$action      = Routes::param('action');

	if ($action !== null) {
		if ($action == 'create') {
			$patcher = Patcher::instance();
			try {
				$patcher
					->setTargetPath($path)
					->setPatchPath($patchFolder)
					->setLastUpdateTime($date)
					->setIgnored(array(
						'application/cache/(.*)',
						'application/logs/(.*)',
						'uploads/(.*)',
						'.idea/(.*)',
						'.git/(.*)',
					))
					->run();

				$data['affectedFilesCount'] = $patcher->getAffectedFiles();
				$data['preparedFiles']      = $patcher->getPreparedFiles();
			}
			catch (\vendor\patcher\Exception $e) {
				$error = $e->getMessage();
			}
			catch (Exception $e) {
				$error = $e->getMessage();
			}

			if ($error !== false) {
				echo $error;
				exit;
			}

			View::make('menu/view/create', $data);
		}
	}
	else {
		View::make('menu/view/index');
	}