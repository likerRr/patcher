<?php
	$post = $_POST;
	$json = array();
	$error = false;

	if (!empty($post)) {
		try {
			$projectPath  = $post['project'];
			$patchPath    = $post['patch'];
			/** @var string $lut - Last update time */
			$lastUpdate   = strtotime($post['datetime']);
			$ignoredFiles = explode("\n", $post['ignored']);
			$patcher = Patcher::instance();
			$patcher
				->setTargetPath($projectPath)
				->setPatchPath($patchPath)
				->setLastUpdateTime($lastUpdate)
				->setIgnored($ignoredFiles)
				->run();

			$affectedFilesCount     = $patcher->getAffectedFiles();
			$data['preparedFiles']  = $patcher->getPreparedFiles();
			$data['projectName']    = $patcher->getProjectName();
			$data['projectPath']    = $projectPath;
			$data['lastUpdateTime'] = $lastUpdate;
			$data['patchPath']      = $patchPath;
			$data['affectedFilesCount'] = $affectedFilesCount;
			ob_start();
			Attach::source('application/templates/logs/patch/created.php', $data);
			$logDump = ob_get_flush();
			ob_clean();

			$json = array(
				'result' => 'success',
				'data' => array(
					'filesCount' => $affectedFilesCount,
					'view' => $logDump,
				),
			);
		}
		catch (\vendor\patcher\Exception $e) {
			$json = array(
				'result' => 'error',
				'message' => $e->getMessage(),
			);
		}
		catch (Exception $e) {
			$json = array(
				'result' => 'error',
				'message' => $e->getMessage(),
			);
		}
	}
	else {
		$json = array(
			'result' => 'error',
			'message' => 'Data is empty',
		);
	}

	echo json_encode($json);
	exit;