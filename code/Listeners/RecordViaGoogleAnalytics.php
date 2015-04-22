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
use Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics\Driver;

class RecordViaGoogleAnalytics extends HttpListener
{
	const VERSION = 1;

	/* @var string */
	public $url = 'https://ssl.google-analytics.com/collect';

	protected $mapping = [
		'version' => 'v',
		'tracking_id' => 'tid',
		'client_id' => 'cid',

		'category' => 'ec',
		'action' => 'ea',
		'label' => 'el',
		'value' => 'ev',

		'eventCategory' => 'ec',
		'eventAction' => 'ea',
		'eventLabel' => 'el',
		'eventValue' => 'ev',

		'order_id' => 'ti',
		'transaction_id' => 'ti',
		'affiliation' => 'ta',
		'revenue' => 'tr',
		'shipping' => 'ts',
		'tax' => 'tt',
		'product' => 'in',
		'price' => 'ip',
		'quantity' => 'iq',
		'code' => 'ic',
		'sku' => 'ic',
		'product_category' => 'iv',
		'currency' => 'cu',
		'product_action' => 'pa',
		'product_action_list' => 'pal',
		'coupon' => 'tcc',
		'checkout_step' => 'cos',
		'checkout_info' => 'col',
		'promo_action' => 'promoa',

		'social_network' => 'sn',
		'social_action' => 'sa',
		'social_target' => 'st',
	];

	public function event(Event $e, $queueName, $params = [])
	{
		$results = [];

		singleton('ea')->executeDrivers(function($driver, $id) use(&$results, $queueName, $params) {
			if(isset($params['id']) && $params['id'] != $id)
				return;

			if(($driver instanceof Driver) && $tid = $driver->setting($id, 'TrackingId')) {
				foreach($params as $type => $value) {
					if(isset($this->mapping[$type])) {
						$params[$this->mapping[$type]] = $value;
						unset($params[$type]);
					}
				}

				$event = isset($params['event']) ? $params['event'] : $queueName;

				$results[] = $this->request([
					'query' => array_merge([
						'v'   => static::VERSION,
						'tid' => $tid,
						'cid' => $driver->session_user_id($id),
						't'   => $event ?: 'pageview',
					], $params)
				]);
			}
		});

		return $results;
	}

	public function ecommerce(Event $e, $queueName, $params = []) {
		return $this->event($e, $queueName, $params);
	}
} 