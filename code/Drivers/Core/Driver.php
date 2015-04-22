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
		return [
			strtoupper($id) . '_JavascriptTemplate' => 'ExternalAnalytics|SiteConfig.core_javascript_template',
		];
	}

	public function javascript($id, ViewableData $controller, $params = [])
	{
		if (!$this->template) {
			$this->template = $this->setting($id, 'JavascriptTemplate',
				BASE_PATH . '/' . SS_EXTERNAL_ANALYTICS_DIR . '/javascript/' . 'core.track.ss.js',
				[
					'objects' => [$controller, $this],
				]
			);
		}

		singleton('assets')->utilities_js();
		return $this->renderWithTemplate($id, $controller, $params);
	}
} 