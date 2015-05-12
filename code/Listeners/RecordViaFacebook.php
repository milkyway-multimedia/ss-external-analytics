<?php
/**
 * Milkyway Multimedia
 * Record.php
 *
 * @package milkywaymultimedia.com.au
 * @author  Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Listeners;

use League\Event\EventInterface as Event;

class RecordViaFacebook extends HttpListener
{
	public function __construct() {
		$this->url = singleton('env')->get('Facebook|ExternalAnalytics.conversion_trackers', [
			'facebook' => [
				'url' => 'https://facebook.com/tr?ev=$id?noscript=1',
			],
		])['facebook']['url'];
	}

	public function event(Event $e = null, $queueName = '', $params = [])
	{
		$results = [];

		$type = isset($params['type']) ? $params['type'] : $queueName;
		$id = isset($params['id']) ? $params['id'] : singleton('env')->get('Facebook.conversion_id_for_' . $type);

		if($id) {
			$url = str_replace('$id', $id, $this->url());
			$oldUrl = $this->url;
			$this->url = $url;

			$results[] = $this->request([
				'query' => $params,
			]);

			$this->url = $oldUrl;
		}

		return $results;
	}

	public function ecommerce(Event $e, $queueName, $params = []) {
		return $this->event($e, $queueName, $params);
	}
} 