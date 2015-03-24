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
use SSViewer;
use Member;
use RandomGenerator;
use Cookie;
use Permission;

class GoogleAnalytics implements Contract {
	protected $template;

	public function __construct($template = '') {
		$this->teplate = $template;
	}

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
		if(Member::currentUserID()) return Member::currentUserID();
		if(headers_sent()) return false;

		$cid = Cookie::get($this->prefix() . '_cid');

		if(!$cid) {
			$generator = new RandomGenerator();
			$cid = $generator->randomToken();
			$cid = substr($cid, 0, 77);
		}

		Utilities::set_cookie($this->prefix() . '_cid', $cid, 730);

		return $cid;
	}

	public function javascript($controller, $params = []) {
		$params = array_merge(['TrackingId' => \Milkyway\SS\ExternalAnalytics\Utilities::env_value('TrackingId', $controller, $this)], $params);

		if(!$params['TrackingId'])
			return '';

		if(!$this->template) {
			$this->template = singleton('env')->get('GoogleAnalytics.javascript_template', [$this], BASE_PATH . '/' . SS_EXTERNAL_ANALYTICS_DIR . '/javascript/' . 'google-analytics.track.init.ss.js');
		}

		if($this->template && !is_array($this->template) && substr($this->template, -3) !== '.ss') {
			if(isset($_GET['showtemplate']) && $_GET['showtemplate'] && Permission::check('ADMIN')) {
				$lines = file($this->template);
				echo "<h2>Template: $this->template</h2>";
				echo "<pre>";
				foreach($lines as $num => $line) {
					echo str_pad($num+1,5) . htmlentities($line, ENT_COMPAT, 'UTF-8');
				}
				echo "</pre>";
			}

			return SSViewer::execute_string(file_get_contents($this->template), ArrayData::create($params));
		}
		else
			return ArrayData::create($params)->renderWith($this->template);
	}
} 