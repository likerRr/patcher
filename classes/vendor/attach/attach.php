<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 28.01.14
 * Time: 14:42
 */

namespace vendor\attach;

class Attach {

	/**
	 * @param $file
	 * @param array $data
	 * @param bool $skipFails
	 * @throws \Exception
	 */
	public static function source($file, $data = array(), $skipFails = true) {
		if (is_array($file)) {
			$files = $file;
			foreach ($files as $key => $file) {
				if (is_file($file)) {
					if (!empty($data)) {
						extract($data);
					}
					require_once $file;
				}
				else {
					if ($skipFails === true) {
						throw new \Exception('File not found');
					}
				}
			}
		}
		else {
			if (is_file($file)) {
				if (!empty($data)) {
					extract($data);
				}
				include $file;
			}
			else {
				if ($skipFails === true) {
					throw new \Exception('File not found');
				}
			}
		}
	}

	/**
	 * @param $script
	 * @param bool $skipFails
	 * @throws \Exception
	 */
	public static function script($script, $skipFails = true) {
		if (is_array($script)) {
			$scripts = $script;
			foreach ($scripts as $key => $file) {
				$file = 'assets/js/' . $file . '.js';
				if (is_file($file)) {
					echo '<script src="/' . $file . '"></script>';
				}
				else {
					if ($skipFails === true) {
						throw new \Exception('File not found');
					}
				}
			}
		}
		else {
			$script = 'assets/js/' . $script . '.js';
			if (is_file($script)) {
				echo '<script src="/' . $script . '"></script>';
			}
			else {
				if ($skipFails === true) {
					throw new \Exception('File not found');
				}
			}
		}
	}

	/**
	 * @param $style
	 * @param bool $skipFails
	 * @throws \Exception
	 */
	public static function style($style, $skipFails = true) {
		if (is_array($style)) {
			$scripts = $style;
			foreach ($scripts as $key => $file) {
				$file = 'assets/css/' . $file . '.css';
				if (is_file($file)) {
					echo '<link rel="stylesheet" href="/' . $file . '"/>';
				}
				else {
					if ($skipFails !== true) {
						throw new \Exception('File not found');
					}
				}
			}
		}
		else {
			$style = 'assets/css/' . $style . '.css';
			if (is_file($style)) {
				echo '<link rel="stylesheet" href="/' . $style . '"/>';
			}
			else {
				if ($skipFails !== true) {
					throw new \Exception('File not found');
				}
			}
		}
	}

} 