<?php namespace Milkyway\SS\ExternalAnalytics;
/**
 * Milkyway Multimedia
 * GoogleAnalytics.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use RequestHandler;
use Config;

class Core {
	public function executeDrivers($callback) {
		foreach(array_diff_key(
			        (array)singleton('env')->get('ExternalAnalytics.enabled', [], [
				        'on' => Config::FIRST_SET
			        ]),
			        array_flip((array)singleton('env')->get('ExternalAnalytics.disabled', [], [
				        'on' => Config::FIRST_SET
			        ]))
		        ) as $id => $options) {
			$callback(\Object::create($options['driver']), $id);
		}
	}

	protected $_queue = [];
	protected $_unqueuedFor = [];

	public function queue($queue, $params = [], $id = '', RequestHandler $controller = null, $recordViaServer = false) {
		if($recordViaServer || ($controller && $controller->Request && $controller->Request->isAjax())) {
			singleton('Eventful')->fire('ea:'.$queue, $queue, $params, $controller);
			return;
		}

		if(!isset($this->_queue[$queue]))
			$this->_queue[$queue] = [];

		if($id)
			$this->_queue[$queue][$id] = $params;
		else
			$this->_queue[$queue][] = $params;
	}

	public function unqueue($queue, $driverId = '', $idOrValue = null) {
		$value = [];

		if(!isset($this->_queue[$queue]))
			return $value;

		if($driverId && isset($this->_unqueuedFor[$driverId]) && in_array($queue, $this->_unqueuedFor[$driverId]))
			return $value;

		if($driverId && !isset($this->_unqueuedFor[$driverId]))
			$this->_unqueuedFor[$driverId] = [];

		if(!$idOrValue) {
			$value = $this->_queue[$queue];
			if(!$driverId) unset($this->_queue[$queue]);
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

		if($driverId)
			$this->_unqueuedFor[$driverId][] = $queue;

		return $value;
	}

	public function getQueue($name = '') {
		if(!$name)
			return $this->_queue;

		return isset($this->_queue[$name]) ? $this->_queue[$name] : null;
	}

	public function clearQueue() {
		$this->_queue = [];
		return $this;
	}
} 