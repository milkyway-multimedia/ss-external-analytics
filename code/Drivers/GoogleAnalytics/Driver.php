<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;

/**
 * Milkyway Multimedia
 * Driver.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Model\Driver as AbstractDriver;
use ViewableData;

use Milkyway\SS\Utilities;
use Member;
use RandomGenerator;
use Cookie;

class Driver extends AbstractDriver
{
	public static function session_user_id($var) {
		if (Member::currentUserID()) return Member::currentUserID();
		if (headers_sent()) return false;

		$cid = Cookie::get($var . '_cid');

		if (!$cid) {
			$generator = new RandomGenerator();
			$cid = $generator->randomToken();
			$cid = substr($cid, 0, 77);
		}

		Utilities::set_cookie($var . '_cid', $cid, 730);

		return $cid;
	}

	public function title($id)
	{
		return _t('ExternalAnalytics.' . $this->prependId('GOOGLE_ANALYTICS', $id), 'Google Analytics');
	}

	public function db($id)
	{
		return [
			$this->prependId('TrackingId', $id) => 'Varchar(255)',
		];
	}

	public function db_to_environment_mapping($id)
	{
		return array_merge(parent::db_to_environment_mapping($id), [
			$this->prependId('TrackingId', $id) => 'GoogleAnalytics|Google|SiteConfig.ga_tracking_id',
			$this->prependId('JavascriptTemplate', $id) => 'GoogleAnalytics|Google|SiteConfig.ga_javascript_template',
		]);
	}

	public function javascript($id, ViewableData $controller = null, $params = [])
	{
		$params = array_merge(['TrackingId' => $this->setting($id, 'TrackingId', null, [
			'objects' => [$controller, $this]
		])], $params);

		if (!$params['TrackingId'])
			return '';

		if (!$this->template) {
			$this->template = $this->setting($id, 'JavascriptTemplate',
				BASE_PATH . '/' . SS_EXTERNAL_ANALYTICS_DIR . '/javascript/' . 'google-analytics.track.init.ss.js',
				[
					'objects' => [$controller, $this],
				]
			);
		}

		return $this->renderWithTemplate($id, $controller, $params);
	}
} 