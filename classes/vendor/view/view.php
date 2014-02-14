<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 28.01.14
 * Time: 14:48
 */

namespace vendor\view;


class View {

	public static function make($file, $data = null) {
		$file = APPPATH . $file . EXT;
		if (is_file($file)) {
			ob_start();
			if ($data !== null) {
				extract($data);
			}
			require_once $file;

			return ob_get_contents();
		}

		return false;
	}

} 