<?php defined('DOCROOT') or die('Denied direct script access');

class Route_Exception extends Exception {

	public function __construct($message, $code) {
		$this->message = $message;
		$this->code = $code;
//		if ($code == '404') {
//			header('Location: /');
//		}
	}

	/** Show Formatted Exception */
	public function debug($isProduction = true) {
		return $this->compileMessage($isProduction);
	}
	/** Generate Error Message */
	protected function compileMessage($isProduction) {
		$message = '<table id="route-debug">';
		$message .= '<tr><td>Error ' . $this->getCode() . ', ' .$this->getMessage() . '</td></tr>';
		if (!$isProduction) {
			$trace = $this->getTrace();
			$called = $trace[0]['class'] . $trace[0]['type'] . $trace[0]['function'];
			$message .= '<tr><td colspan="2">Detect in ' . $this->getFile() . ':' . $this->getLine() . '</td></tr>';
			$message .= '<tr><td colspan="2">Called in ' . $trace[0]['file'] . ':' . $trace[0]['line'] . ' as ' . $called . '</td></tr>';
		}
		$message .= '</table>';
		$message .= $this->loadDebugCSS();

		return $message;
	}
	/** Load CSS */
	protected function loadDebugCSS() {
		$rules = array(
			'#route-debug' => array(
				'border-spacing' => '0',
				'width' => '50%',
			),
			'#route-debug td' => array(
				'padding' => '5px',
				'font-weight' => 'bold',
			),
		);

		$css = '<style>';
		foreach ($rules as $rule => $options) {
			$css .= $rule . '{';
			foreach ($options as $option => $value) {
				$css .= $option . ':' . $value . ';';
			}
			$css .= '}';
		}
		$css .= '</style>';

		return $css;
	}

} 