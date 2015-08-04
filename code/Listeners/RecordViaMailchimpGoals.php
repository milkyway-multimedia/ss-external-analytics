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
use Milkyway\SS\Director;
use Controller;

class RecordViaMailchimpGoals extends HttpListener
{
	/* @var string */
	public $url = 'https://<dc>.api.mailchimp.com/goal/record-event.json';

	public function event(Event $e, $queueName, $params = [])
	{
		$results = [];

		singleton('ea')->executeDrivers(function($driver, $id) use(&$results, $params) {
			if(isset($params['id']) && $params['id'] != $id)
				return;

			if(!isset($params['email'])) {
				if(Session::get('mc.email_id'))
					$params['email'] = ['euid' => Session::get('mc.email_id')];
				else
					$params['email'] = ['email' => \Member::currentUser() ? \Member::currentUser()->Email : null];
			}

			if(!isset($params['campaign_id']))
				$params['campaign_id'] = Session::get('mc.campaign_id');

			if(!$params['email'] || $params['campaign_id'])
				return;

			if(($driver instanceof Driver) && $apiKey = $driver->setting($id, 'ApiKey')) {
				$listId = isset($params['list_id']) ? $params['list_id'] : $driver->setting($id, 'default_list_id');

				if(!$listId) {
					return;
				}

				if(!isset($params['event']))
					$params['event'] = Director::absoluteURL(Controller::curr()->Link());

				$parts = explode('-', $apiKey);
				$dataCentre = array_pop($parts);

				$url = str_replace('<dc>', $dataCentre, $this->url());
				$oldUrl = $this->url;
				$this->url = $url;

				$results[] = $this->request([
					'body' => array_merge([
						'apikey'   => $apiKey,
						'list_id' => $listId,
					], $params)
				]);

				$this->url = $oldUrl;
			}
		});

		return $results;
	}

	public function ecommerce(Event $e, $queueName, $params = [])
	{
		if(isset($params['action']) && in_array($params['action'], ['transaction', 'refund']))
			return $this->event($e, $queueName, $params);

		return [];
	}
} 