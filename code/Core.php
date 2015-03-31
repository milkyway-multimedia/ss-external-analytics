<?php namespace Milkyway\SS\ExternalAnalytics;
/**
 * Milkyway Multimedia
 * GoogleAnalytics.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use RequestHandler;

class Core {
	public function executeDrivers($callback) {
		foreach(array_diff_key(
			        (array)singleton('env')->get('ExternalAnalytics.enabled'),
			        array_flip((array)singleton('env')->get('ExternalAnalytics.disabled'))
		        ) as $id => $options) {
			$callback(\Object::create($options['driver']), $id);
		}
	}

	protected $_queue = [];

	public function queue($queue, $params = [], $id = '', RequestHandler $controller = null, $recordViaServer = false) {
		if($recordViaServer || ($controller && $controller->Request && $controller->Request->isAjax())) {
			singleton('Eventful')->fire('ea:'.$queue, $params, $controller);
			return;
		}

		if(!isset($this->_queue[$queue]))
			$this->_queue[$queue] = [];

		if($id)
			$this->_queue[$queue][$id] = $params;
		else
			$this->_queue[$queue][] = $params;
	}

	public function unqueue($queue, $idOrValue = null) {
		$value = [];

		if(!isset($this->_queue[$queue]))
			return $value;

		if(!$idOrValue) {
			$value = $this->_queue[$queue];
			unset($this->_queue[$queue]);
		}
		else if(is_string($idOrValue) && isset($this->_queue[$queue][$idOrValue])) {
			$value = $this->_queue[$queue][$idOrValue];
			unset($this->_queue[$queue][$idOrValue]);
		}
		else {
			$key = array_search($idOrValue, $this->_queue[$queue]);

			if($key !== false) {
				$value = $this->_queue[$queue][$key];
				unset($this->_queue[$queue][$key]);
			}
		}

		return $value;
	}
} 