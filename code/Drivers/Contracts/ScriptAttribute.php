<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Contracts;
/**
 * Milkyway Multimedia
 * ScriptAttribute.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use ViewableData;

interface ScriptAttribute {
	public function output(Driver $driver, $id, ViewableData $controller = null, $params = []);
} 