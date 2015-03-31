<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Mailchimp;

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
		return _t('ExternalAnalytics.' . strtoupper($id) . '_MAILCHIMP', 'Mailchimp (Goals and Ecommerce360)');
	}

	public function db($id)
	{
		return [
			strtoupper($id) . '_UUId' => 'Varchar(255)',
		];
	}

	public function db_to_environment_mapping($id)
	{
		return array_merge(parent::db_to_environment_mapping($id), [
			'ApiKey' => 'Mailchimp|EmailCampaigns|SiteConfig.mc_api_key',
			strtoupper($id) . '_UUId' => 'Mailchimp|SiteConfig.mc_uuid',
			strtoupper($id) . '_JavascriptTemplate' => 'Mailchimp|SiteConfig.mc_javascript_template',
		]);
	}

	public function javascript($id, ViewableData $controller, $params = [])
	{
		$params = array_merge(['UUID' => $this->setting($id, 'UUID', null, [
			'objects' => [$controller, $this]
		])], $params);

		if (!$params['UUID'])
			return '';

		if (!$this->template) {
			$this->template = $this->setting($id, 'JavascriptTemplate',
				BASE_PATH . '/' . SS_EXTERNAL_ANALYTICS_DIR . '/javascript/' . 'mailchimp.goals.init.ss.js',
				[
					'objects' => [$controller, $this],
				]
			);
		}

		return $this->renderWithTemplate($id, $controller, $params);
	}
} 