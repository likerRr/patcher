<?php
	$post = $_POST;
	$json = array();
	define('DIRECTORY_NOT_EXISTS', 1);

	if (!empty($_POST)) {
		$project = $post['project'];
		if (is_dir($project)) {
			$gitignoreFile = $project . SEPARATOR . '.gitignore';
			$projectName = basename($project);
			$patchesPath = dirname($project) . SEPARATOR . '_patches' . SEPARATOR . strtolower($projectName) . SEPARATOR;
			$patchPath = $patchesPath . date('d.m.Y');
			$versionFile = $patchesPath . '.version';
			$lastUpdateTime = date('d.m.Y') . ' 00:00:00';
			$ignored = '';
			if (is_file($versionFile)) {
				$versionJSON = json_decode(file_get_contents($versionFile), true);
				$lastUpdateTime = date('d.m.Y H:i:s', $versionJSON['time']);
			}
			if (is_file($gitignoreFile)) {
				$ignored = file_get_contents($gitignoreFile);
			}
			$json = array(
				'result' => 'success',
				'ignored' => $ignored,
				'patchPath' => $patchPath,
				'lastUpdateTime' => $lastUpdateTime,
			);
		}
		else {
			$json = array(
				'result' => 'error',
				'code' => DIRECTORY_NOT_EXISTS,
			);
		}
	}

	echo json_encode($json);
	exit;