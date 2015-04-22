<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Mailchimp;

/**
 * Milkyway Multimedia
 * Driver.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\ManagesFields;
use Milkyway\SS\ExternalAnalytics\Drivers\Model\Driver as AbstractDriver;
use ViewableData;
use ClassInfo;
use SiteConfig;

class Driver extends AbstractDriver implements ManagesFields
{
	public function title($id)
	{
		return _t('ExternalAnalytics.' . $this->prependId('MAILCHIMP', $id), 'Mailchimp (Goals and Ecommerce360)');
	}

	public function db($id)
	{
		return [
			$this->prependId('UUId', $id) => 'Varchar(255)',
			$this->prependId('StoreId', $id) => 'Varchar(255)',
			$this->prependId('StoreName', $id) => 'Varchar(255)',
		];
	}

	public function db_to_environment_mapping($id)
	{
		return array_merge(parent::db_to_environment_mapping($id), [
			$this->prependId('ApiKey', $id) => 'ExternalAnalytics|Mailchimp|EmailCampaigns|SiteConfig.mc_api_key',
			$this->prependId('StoreId', $id) => 'ExternalAnalytics|Mailchimp|EmailCampaigns|SiteConfig.mc_store_id',
			$this->prependId('StoreName', $id) => 'ExternalAnalytics|Mailchimp|EmailCampaigns|SiteConfig.mc_store_name',
			$this->prependId('UUId', $id) => 'ExternalAnalytics|Mailchimp|SiteConfig.mc_uuid',
			$this->prependId('JavascriptTemplate', $id) => 'ExternalAnalytics|Mailchimp|SiteConfig.mc_javascript_template',
		]);
	}

	public function javascript($id, ViewableData $controller, $params = [])
	{
		$params = array_merge([
			'uuid' => $this->setting($id, 'UUId', null, [
				'objects' => [$controller, $this]
			]),
			'dc' => $this->setting($id, 'dc', 'us10', [
				'objects' => [$controller, $this]
			]),
		], $params);

		if (!$params['uuid'])
			return '';

		$params['Settings'] = json_encode(array_intersect_key($params, array_flip(['uuid', 'dc'])));

		if (!$this->template) {
			$this->template = $this->setting($id, 'JavascriptTemplate',
				BASE_PATH . '/' . SS_EXTERNAL_ANALYTICS_DIR . '/javascript/' . 'mailchimp.goals.init.ss.js',
				[
					'objects' => [$controller, $this],
				]
			);
		}

		singleton('assets')->utilities_js();
		return $this->renderWithTemplate($id, $controller, $params);
	}

	public function fieldManager($id, $fields) {
		if($uuid = $fields->fieldByName($this->prependId('UUId', $id))) {
			$uuid->setDescription(_t('ExternalAnalytics.DESC-UUID', '<a href="{link}">This UU ID can be found in your Mailchimp account under Integrations, once you have enabled the Goal module</a>', [
				'link' => 'https://us10.admin.mailchimp.com/account/integrations/#facebook',
			]));
		}

		return $fields;
	}

	protected function getOtherDefaultForSetting($setting, $id) {
		$siteConfig = ClassInfo::exists('SiteConfig') ? SiteConfig::current_site_config() : null;

		if($setting == 'StoreId') {
			return substr($this->setting($id, 'StoreName'), 0, 31);
		}

		if($setting == 'StoreName') {
			$title = '';

			if($siteConfig)
				$title = $siteConfig->AdminName ?: $siteConfig->Title;

			if(!$title)
				$title = singleton('LeftAndMain')->ApplicationName;

			return $title ?: 'SS Store';
		}

		return parent::getOtherDefaultForSetting($setting, $id);
	}
} 