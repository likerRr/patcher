<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 07.02.14
 * Time: 13:55
 */

namespace vendor\checker;

use vendor\patcher\Exception;

class Checker {

	/** @var Checker */
	protected static $instance = null;

	protected $patchPath = null;

	protected $type = null;

	protected $lines = array();

	protected $availableTypes = array(
		'git',
		'plain',
	);

	/**
	 * Files created while prepare lines
	 * @var array
	 */
	protected $created = array();

	/**
	 * Deleted files while prepare lines
	 * @var array
	 */
	protected $deleted = array();

	/**
	 * Edited files while prepare lines
	 * @var array
	 */
	protected $edited = array();

	/**
	 * All files while prepare lines
	 * @var array
	 */
	protected $files = array();

	/**
	 * Created/edited/deleted/broken files count
	 * @var int
	 */
	protected $affectedFiles = 0;

	/**
	 * Result array with statistics
	 * @var array
	 */
	protected $checked = array(
		'created' => array(),
		'deleted' => array(),
		'edited' => array(),
		'broken' => array(),
	);

	/** Singleton */
	public static function instance() {
		if (Checker::$instance === null) {
			Checker::$instance = new Checker();
		}


		return Checker::$instance;
	}

	public function getAffectedFiles() {
		return $this->affectedFiles;
	}

	public function setPatchPath($path) {
		$this->patchPath = $path;

		return $this;
	}

	public function setType($type) {
		$this->type = $type;

		return $this;
	}

	public function setText($text) {
		$this->lines = explode("\n", $text);

		return $this;
	}

	public function check() {
		if ($this->validate()) {
			$type = $this->type;
			return call_user_func_array(array($this, 'check' . ucfirst($type)), array());
		}
		else {
			throw new Exception('Something Wrong');
		}
	}

	protected function getRealPath($relativePath) {
		$path = $this->patchPath . SEPARATOR . $relativePath;

		return realpath($path);
	}

	public function prepareFiles() {
		$lines = $this->lines;
		foreach ($lines as $line) {
			$line = trim($line);
			if (!empty($line)) {
				$file = $line;
				if (strpos($file, '*created* ') === 0) {
					$this->created[] = substr($file, 10);
				}
				elseif (strpos($file, '*deleted* ') === 0) {
					$this->deleted[] = substr($file, 10);
				}
				elseif (strpos($file, '*edited* ') === 0) {
					$this->files[] = substr($file, 9);
				}
			}
		}

		return $this;
	}

	protected function checkGit() {
		$patch = $this->patchPath;
		$files = $this->files;

		foreach ($files as $file) {
			$fileName = $patch . $file;
			$this->affectedFiles += 1;
			if (is_file($fileName)) {
				if ($this->isCreated($file)) {
					$this->checked['created'][] = $file;
				}
				else {
					$this->checked['edited'][] = $file;
				}
			}
			else {
				if ($this->isDeleted($file)) {
					$this->checked['deleted'][] = $file;
				}
				else {
					$this->checked['broken'][] = $file;
				}
			}
		}

		return $this->checked;
	}

	protected function isCreated($file) {
		if (in_array($file, $this->created)) {
			return true;
		}

		return false;
	}

	protected function isDeleted($file) {
		if (in_array($file, $this->deleted)) {
			return true;
		}

		return false;
	}

	protected function validate() {
		if (!in_array($this->type, $this->availableTypes)) {
			throw new Exception('Invalid Type');
		}
		if (!is_dir($this->patchPath)) {
			throw new Exception('Invalid Patch Path');
		}
		if (empty($this->lines)) {
			throw new Exception('Text to check is empty');
		}

		return true;
	}

}