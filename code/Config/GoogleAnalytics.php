<?php
/**
 * Milkyway Multimedia
 * Config.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Config;

use Milkyway\SS\Utilities;
use ArrayData;

class GoogleAnalytics implements Contract {
	protected $template = 'IncludeJavascript_GoogleAnalytics';

	public function i18n_title() {
		return _t('ExternalAnalytics.GOOGLE_ANALYTICS', 'Google Analytics');
	}

	public function prefix() {
		return 'ga';
	}

	public function db()
	{
		return [
			'TrackingId'               => 'Varchar(255)',
			'ApiClientId'               => 'Varchar(255)',
		];
	}

	public function map()
	{
		return [
			'TrackingId'               => 'tracking_id',
			'ApiClientId'               => 'api_client_id',
			'ReportsAccountId'               => 'reports_account_id',
		];
	}

	public function findClientId() {
		if(\Member::currentUserID()) return \Member::currentUserID();
		if(headers_sent()) return false;

		$cid = \Cookie::get('__' .  $this->prefix() . '_cid');

		if(!$cid) {
			$generator = new \RandomGenerator();
			$cid = $generator->randomToken();
			$cid = substr($cid, 0, 77);
		}

		Utilities::set_cookie('__' .  $this->prefix() . '_cid', $cid, 730);

		return $cid;
	}

	public function javascript($controller, $params = []) {
		$params = array_merge(['TrackingId' => \Milkyway\SS\ExternalAnalytics\Utilities::env_value('TrackingId', $controller, $this)], $params);
		return isset($params['TrackingId']) ? ArrayData::create($params)->renderWith($this->template) : '';
	}
} 