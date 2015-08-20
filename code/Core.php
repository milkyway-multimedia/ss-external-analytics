<?php namespace Milkyway\SS\ExternalAnalytics;

/**
 * Milkyway Multimedia
 * Core.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use RequestHandler;
use Config;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver;

class Core
{
    protected $configuration = [];
    protected $loadedInConfigurationDefaults = false;

    public function configuration()
    {
        $this->loadInConfigurationDefaults();
        return $this->configuration;
    }

    public function configure($name, $value = null)
    {
        $this->loadInConfigurationDefaults();
        $currentValue = array_get($this->configuration, $name);
        if (is_array($currentValue)) {
            array_set($this->configuration, $name, array_merge($currentValue, (array)$value));
        } else {
            array_set($this->configuration, $name, $value);
        }
        return $this->configuration();
    }

    protected function loadInConfigurationDefaults()
    {
        if ($this->loadedInConfigurationDefaults) {
            return;
        }

        $this->configuration = singleton('env')->get('ExternalAnalytics.json_configuration');
        $this->loadedInConfigurationDefaults = true;
    }

    public function executeDrivers($callback)
    {
        $output = [];

        foreach (array_diff_key(
                     (array)singleton('env')->get('ExternalAnalytics.enabled', [], [
                         'on' => Config::FIRST_SET,
                     ]),
                     array_flip((array)singleton('env')->get('ExternalAnalytics.disabled', [], [
                         'on' => Config::FIRST_SET,
                     ]))
                 ) as $id => $options) {
            $output[] = $callback(singleton($options['driver']), $id);
        }

        return array_filter($output);
    }

    public function executeDriverAttributes($callback, Driver $driver = null, $driverId = '', $params = [])
    {
        if ($driver && $driverId) {
            return $this->handleAttributes($callback, $driver, $driverId, $params);
        }

        $output = [];

        $this->executeDrivers(function (Driver $driver, $id) use ($callback, $params, $output) {
            $output[] = $this->handleAttributes($callback, $driver, $id, $params);
        });

        return array_filter($output);
    }

    protected function handleAttributes($callback, Driver $driver, $id, $params = [])
    {
        $output = [];

        foreach (array_diff(
                     (array)$driver->configuration($id)['attributes'],
                     (array)$driver->configuration($id)['disabled_attributes'],
                     (array)$driver->setting($id, 'DisabledScriptAttributes')
                 ) as $class) {
            $output[] = $callback(singleton($class), $driver, $id, $params);
        }

        return array_filter($output);
    }

    protected $_queue = [];
    protected $_unqueuedFor = [];

    public function fireAllQueues()
    {
        foreach ($this->_queue as $queue => $items) {
            foreach ($items as $item) {
                singleton('Eventful')->fire('ea:' . $queue, $queue, $item);
            }
        }

        $this->clearQueue();
    }

    public function queue($queue, $params = [], $id = '', RequestHandler $controller = null, $recordViaServer = false)
    {
        if ($recordViaServer) {
            singleton('Eventful')->fire('ea:' . $queue, $queue, $params, $controller);
            return;
        }

        if (!isset($this->_queue[$queue])) {
            $this->_queue[$queue] = [];
        }

        if ($id) {
            $this->_queue[$queue][$id] = $params;
        } else {
            $this->_queue[$queue][] = $params;
        }
    }

    public function unqueue($queue, $driverId = '', $idOrValue = null)
    {
        $value = [];

        if (!isset($this->_queue[$queue])) {
            return $value;
        }

        if ($driverId && isset($this->_unqueuedFor[$driverId]) && in_array($queue, $this->_unqueuedFor[$driverId])) {
            return $value;
        }

        if ($driverId && !isset($this->_unqueuedFor[$driverId])) {
            $this->_unqueuedFor[$driverId] = [];
        }

        if (!$idOrValue) {
            $value = $this->_queue[$queue];
            if (!$driverId) {
                unset($this->_queue[$queue]);
            }
        } else {
            if (is_string($idOrValue) && isset($this->_queue[$queue][$idOrValue])) {
                $value = $this->_queue[$queue][$idOrValue];
                unset($this->_queue[$queue][$idOrValue]);
            } else {
                $key = array_search($idOrValue, $this->_queue[$queue]);

                if ($key !== false) {
                    $value = $this->_queue[$queue][$key];
                    unset($this->_queue[$queue][$key]);
                }
            }
        }

        if ($driverId) {
            $this->_unqueuedFor[$driverId][] = $queue;
        }

        return $value;
    }

    public function getQueue($name = '')
    {
        if (!$name) {
            return $this->_queue;
        }

        return isset($this->_queue[$name]) ? $this->_queue[$name] : null;
    }

    public function clearQueue()
    {
        $this->_queue = [];
        return $this;
    }
} 