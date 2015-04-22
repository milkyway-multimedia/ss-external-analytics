<?php
/**
 * Milkyway Multimedia
 * Controller.php
 *
 * @package milkywaymultimedia.com.au
 * @author  Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics;

use Milkyway\SS\Director;
use Session;

class Controller extends \Controller
{
	public function index(\SS_HTTPRequest $request)
	{
		if ($this->Response)
			$this->Response->setStatusCode(200);

		if ($request && $request->isAjax()) {
			$vars = [
				'site_start' => Session::get('ea.site_start'),
				'page_start' => Session::get('ea.page_start'),
			];

			$getVarsToSession = singleton('env')->get('ExternalAnalytics.get_vars_to_session');

			array_walk($getVarsToSession, function($options, $sessionVar) use(&$vars) {
				$title = isset($options['title']) ? $options['title'] : $sessionVar;
				$vars[$title] = Session::get($sessionVar);
			});

			return Director::ajax_response($vars);
		}

		return [];
	}

	public function Link() {
		return Director::absoluteURL('/' . (string)array_search(trim(__CLASS__, '\\'), \Config::inst()->forClass('Director')->rules));
	}
} 