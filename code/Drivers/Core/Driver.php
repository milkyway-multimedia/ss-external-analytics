<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Core;

/**
 * Milkyway Multimedia
 * Driver.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Model\Driver as AbstractDriver;
use ViewableData;

class Driver extends AbstractDriver
{
	public function title($id)
	{
		return _t('ExternalAnalytics.' . strtoupper($id) . '_CORE', 'Core');
	}

	public function db($id)
	{
		return [];
	}

	public function db_to_environment_mapping($id)
	{
		return array_merge(parent::db_to_environment_mapping($id), [
			$this->prependId('ConversionTracking', $id) => 'Conversions_' . $id . '|ExternalAnalytics.conversion_trackers',
		]);
	}
} 