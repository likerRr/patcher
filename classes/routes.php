<?php defined('DOCROOT') or die('Denied direct script access');

	class Routes {
		/** Array of Routes */
		protected static $routes;
		/** Detected Route Name */
		protected static $detectedRoute;
		/** Route URL */
		protected $_url = null;
		/** Route Params, i.e. id => regExp */
		protected $_params = null;
		/** Route File */
		protected $_regExp = null;
		/** Route RegExp */
		protected $_file = null;
		/** Founded Params by Route URL */
		protected static $_matchedParams = null;
		/** Path to Load Files */
		protected static $_filePath = '';

		public function __construct($defaults) {
			$this->_url    = $defaults['url'];
			$this->_params = $defaults['params'];
			$this->_regExp = $defaults['regExp'];
		}
		/**
		 * Add Route
		 * @param $name
		 * @param $url
		 * @param null $params
		 * @return Routing
		 */
		public static function add($name, $url, $params = null) {
			return Routes::$routes[$name] = new Routing(array(
				'url' => $url,
				'params' => $params,
				'regExp' => Routes::compile($url, $params),
			));
		}
		/**
		 * Set Files Path Location
		 * @param $path
		 */
		public static function setAppPath($path) {
			Routes::$_filePath = $path;
		}
		/**
		 * Attach File To Route
		 * @param $name
		 */
		public function file($name) {
			$fileName = DOCROOT . Routes::$_filePath . $name . '.php';
			if (file_exists($fileName)) {
				$this->_file = $fileName;
			}
		}
		/**
		 * Get Registered Routes
		 * @return mixed
		 */
		public static function all() {
			return Routes::$routes;
		}
		/**
		 * Detect Route and Load Page
		 */
		public static function detect() {
			/** Decode Incoming Url */
			$requestUri = rawurldecode($_SERVER['REQUEST_URI']);
			foreach (Routes::$routes as $key => $val) {
				if (preg_match('!^\/' . $val->_regExp . '$!', $requestUri, $matches)) {
					Routes::$detectedRoute = $key;
					Routes::$_matchedParams = $matches;
					break;
				}
			}

			$file = isset(Routes::$routes[Routes::$detectedRoute]) ? Routes::$routes[Routes::$detectedRoute]->_file : APPPATH . $requestUri . EXT;
			if (!is_file($file)) {
				throw new Route_Exception('Route Not Found', 404);
			}

			ob_start();
			if (!@include_once $file) {
				ob_end_clean();
				throw new Route_Exception('Cant\' include file', 404);
			}

			return ob_get_clean();
		}
		/**
		 * Compile Route Url To RegExp
		 * @param $url
		 * @param $params
		 * @return string
		 */
		protected static function compile($url, $params) {
			/** Escape special chars for regular expression */
			$url = addcslashes($url, '^[.${*+?/');
			$routeUrl = $url;
			/** Make closed bracket as optional parameter */
			$routeUrl = str_replace(')', ')?', $routeUrl);
			if (!empty($params)) {
				foreach ($params as $key => $val) {
					$routeUrl = str_replace('<' . $key . '>', '(?J)(?P<'.$key.'>' . $val . ')', $routeUrl);
				}
			}
			/** Add last not required / in the end of query */
			$routeUrl = $routeUrl . '\/?';
			return $routeUrl;
		}
		/**
		 * Get Current Route by Name or Instance
		 * @param string $type
		 * @return bool
		 */
		public static function current($type = 'name') {
			if ($type == 'name') {
				return Routes::$detectedRoute;
			}
			elseif ($type == 'route') {
				return Routes::$routes[Routes::$detectedRoute];
			}
			else {
				return false;
			}
		}

		/**
		 * Get Param from URL by Name
		 * @param $name
		 * @param null $default
		 * @return null
		 */
		public static function param($name, $default = null) {
			if (isset(Routes::$_matchedParams[$name])) {
				return Routes::$_matchedParams[$name];
			}

			return $default;
		}
		/**
		 * @param $method
		 * @param $params
		 */
		public static function __callStatic($method, $params) {
			if (!method_exists('Routes', $method)) {
				die('<h2>Exception! Can\'t load static method ' . $method . '</h2>');
			}
		}
	}