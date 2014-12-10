<?php
/**
 * Milkyway Multimedia
 * TimingsRequestFilter.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Api;

class TimingsRequestFilter implements \RequestFilter {
	public function preRequest(\SS_HTTPRequest $request, \Session $session, \DataModel $model) {
		if(!$session->get('ea.site_start')) {
			$session->set('ea.site_start', time());
			$session->set('ea.session_started', time());
		}
		else {
			$session->clear('ea.session_started');
		}

		$session->set('ea.page_start', time());
	}

	public function postRequest(\SS_HTTPRequest $request, \SS_HTTPResponse $response, \DataModel $model) {

	}
} 