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

class RecordViaGoogleAdwords extends HttpListener
{
	public function __construct() {
		$this->url = singleton('env')->get('GoogleAdwords|ExternalAnalytics.adwords_conversion_url', 'https://www.googleadservices.com/pagead/conversion/$id?script=0');
	}

	public function event(Event $e = null, $queueName = '', $params = [])
	{
		$results = [];

		if(isset($params['adwords']))
			$params = $params['adwords'];

		$type = isset($params['type']) ? $params['type'] : $queueName;
		$id = isset($params['id']) ? $params['id'] : singleton('env')->get('GoogleAdwords.conversion_id_for_' . $type);

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

	public function conversion(Event $e, $queueName, $params = []) {
		return $this->event($e, $queueName, $params);
	}
} 