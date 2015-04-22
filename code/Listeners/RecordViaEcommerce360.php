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
use Milkyway\SS\ExternalAnalytics\Drivers\Mailchimp\Driver;
use Session;

class RecordViaEcommerce360 extends HttpListener
{
	/* @var string */
	public $url = 'https://<dc>.api.mailchimp.com/ecomm/order-add.json';

	public function ecommerce(Event $e = null, $queueName = '', $params = [])
	{
		$results = [];

		singleton('ea')->executeDrivers(function($driver, $id) use(&$results, $params) {
			if(isset($params['driver_id']) && $params['driver_id'] != $id)
				return;

			if(isset($params['driver_id']))
				unset($params['driver_id']);

			if(!isset($params['email_id'])) {
				if(Session::get('mc.email_id'))
					$params['email_id'] = Session::get('mc.email_id');
				else if(!isset($params['email'])) {
					$params['email'] = \Member::currentUser() ? \Member::currentUser()->Email : null;
				}
			}

			if(!isset($params['campaign_id']))
				$params['campaign_id'] = Session::get('mc.campaign_id');

			if(!$params['email_id'] || $params['campaign_id'])
				return;

			if(($driver instanceof Driver) && $apiKey = $driver->setting($id, 'ApiKey')) {
				$paramDefaults = [
					'total' => singleton('env')->get('Ecommerce360|Mailchimp|ExternalAnalytics.conversion_value', 0.00),
					'store_id' => $driver->setting($id, 'StoreId'),
					'store_name' => $driver->setting($id, 'StoreName'),
					'order_date' => date('Y-m-d'),
				];

				$parts = explode('-', $apiKey);
				$dataCentre = array_pop($parts);

				$url = str_replace('<dc>', $dataCentre, $this->url());
				$oldUrl = $this->url;
				$this->url = $url;

				$results[] = $this->request([
					'body' => [
						'apikey'   => $apiKey,
						'order' => array_merge($paramDefaults, $params),
					],
				]);

				$this->url = $oldUrl;
			}
		});

		return $results;
	}
} 