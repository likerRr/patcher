<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 24.01.14
 * Time: 10:54
 */

namespace vendor\patcher;


class Patcher {

	/** @var Patcher */
	protected static $instance = null;

	/** @var string - Path to scanned directory */
	protected $targetPath      = null;

	/** @var int - Scanned directory last update time */
	protected $lastUpdateTime  = 0;

	/** @var string - Path where patch will put */
	protected $patchPath;

	/** @var array - Prepared for copy files */
	protected $preparedFiles   = array();

	/** @var bool - If true, $patchPath can not be inside $targetPath */
	protected $preventRootDir  = true;

	protected $affectedFiles   = 0;

	/** @var array - Files and folders where ignored (support regexp). Must starts from root target directory */
	protected $ignored = array();

	/** @var null Project name is basedir of targetPath */
	protected $projectName = null;

	/** Singleton */
	public static function instance() {
		if (Patcher::$instance === null) {
			Patcher::$instance = new Patcher();
		}

		return Patcher::$instance;
	}

	/** Prevent create object */
	private function __construct() {}

	/**
	 * Set targeted path to scan.
	 * Patch path creates automatically <APP-NAME>_PATCH_<DATE>, but can be changed via @method setPatchPath
	 * @param $path
	 * @return $this
	 * @throws Exception
	 */
	public function setTargetPath($path) {
		if (is_dir($path)) {
			$this->targetPath  = realpath($path);
			$parts             = explode('\\', $path);
			$this->projectName = strtoupper(end($parts));
			// set up default patch path
			$this->setPatchPath($this->patchPath = $path . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $this->projectName . '_PATCH_' . date('d.m.Y'));
		}
		else {
			throw new Exception('Path not found');
		}

		return $this;
	}

	/**
	 * Set up path, where patch was saved
	 * @param $patchPath
	 * @return $this
	 * @throws Exception
	 */
	public function setPatchPath($patchPath) {
		if (!empty($patchPath)) {
			if ($this->preventRootDir === true) {
				if (is_dir($this->targetPath)) {
					$patchParentDir = dirname($patchPath);
					if (strpos($this->targetPath, $patchParentDir) !== false) {
						throw new Exception('You can not include patch folder into targeted folder');
					}
					else {
						$this->patchPath = $patchPath;
					}
				}
				else {
					throw new Exception('Please, set target path, before setting up patch path');
				}
			}
			else {
				$this->patchPath = $patchPath;
			}
		}

		return $this;
	}

	/**
	 * Sel last $this->path update time
	 * @param $time
	 * @return $this
	 * @throws Exception
	 */
	public function setLastUpdateTime($time) {
		if (is_int($time)) {
			$this->lastUpdateTime = $time;
		}
		else {
			throw new Exception('Last update date must be UNIX timestamp');
		}

		return $this;
	}

	/**
	 * Find new files and folders
	 * @param $dir
	 */
	protected function scan($dir) {
		// ignore patch folder
		if (strpos($dir, $this->patchPath) == 0) {
			foreach (scandir($dir) as $key => $val) {
				$file = $dir . '\\' . $val;
				if ($this->isIgnore($file) === false) {
					if ($val != '.' && $val != '..') {
						if (is_dir($file)) {
							$this->scan($file, $this->lastUpdateTime);
						}
						else {
							if (filemtime($file) >= $this->lastUpdateTime || filectime($file) >= $this->lastUpdateTime) {
								$this->preparedFiles[$dir][] = $val;
							}
						}
					}
				}
			}
		}
	}

	protected function isIgnore($str) {
		$str = str_replace('\\', '/', $str);
		$targetPath = str_replace('\\', '/', $this->targetPath);
		foreach ($this->ignored as $pattern) {
			if (preg_match('|^' . $targetPath . '/' . $pattern . '|', $str)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Make patch and save in $this->patchPath folder
	 */
	protected function makePatch() {
		$patchPath = $this->patchPath;
		if (!empty($patchPath)) {
			if (is_dir($patchPath)) {
				$this->delTree($patchPath);
			}
			mkdir($patchPath);
			$this->affectedFiles = 0;
			foreach ($this->preparedFiles as $filePath => $pathData) {
				if (is_array($pathData)) {
					foreach ($pathData as $key2 => $file) {
						$fileDir = str_replace($this->targetPath, '', $filePath);
						mkdir($patchPath . $fileDir, 0777, true);
						copy($this->targetPath . $fileDir . DIRECTORY_SEPARATOR . $file, $patchPath . $fileDir . DIRECTORY_SEPARATOR . $file);
						$this->affectedFiles++;
					}
				}
			}
			file_put_contents(dirname($patchPath) . SEPARATOR . '.version', '{"time": "' . time() . '"}');
		}
	}

	/**
	 * Check if root dir prevented
	 * @return bool
	 */
	public function isRootDirPrevented() {
		return $this->preventRootDir;
	}

	/**
	 * When needs to disable save patch in targeted folder
	 * @param boolean $preventRootDir
	 * @return $this
	 */
	public function preventRootDir($preventRootDir) {
		$this->preventRootDir = $preventRootDir;

		return $this;
	}

	/**
	 * Recursive deletion folders
	 * @param $dir
	 * @return bool
	 */
	protected function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	/**
	 * Scan target folder and make patch
	 * @throws Exception
	 */
	public function run() {
		try {
			$this->scan($this->targetPath);
			$this->makePatch();
		}
		catch (\Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * @return int
	 */
	public function getAffectedFiles() {
		return $this->affectedFiles;
	}

	/**
	 * @return array
	 */
	public function getPreparedFiles()
	{
		return $this->preparedFiles;
	}

	/**
	 * @return array
	 */
	public function getIgnored() {
		return $this->ignored;
	}

	/**
	 * @param array $ignored
	 * @return $this
	 */
	public function setIgnored($ignored) {
		foreach ($ignored as $key => $rule) {
			$pattern = trim(str_replace('*', '(.*)', $rule));
			$this->ignored[] = $pattern;
		}

		return $this;
	}

	/**
	 * @return null
	 */
	public function getProjectName()
	{
		return $this->projectName;
	}
}