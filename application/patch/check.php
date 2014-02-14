<?php
	$post  = $_POST;
	$json  = array();
	$error = false;

	if (!empty($post)) {
		try {
			$patchPath = $post['path'];
			$textType = $post['type'];
			$text = $post['check'];
			$checker = Checker::instance();
			$checked = $checker
				->setPatchPath($patchPath)
				->setType($textType)
				->setText($text)
				->prepareFiles()
				->check();

			$data = array(
				'checked' => $checked,
				'patchPath' => $patchPath,
				'projectName' => basename($patchPath),
				'affectedFilesCount' => $checker->getAffectedFiles(),
			);
			ob_start();
			Attach::source('application/templates/logs/patch/checked.php', $data);
			$logDump = ob_get_flush();
			ob_clean();

			$json = array(
				'result' => 'success',
				'data' => array(
//					'filesCount' => $affectedFilesCount,
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