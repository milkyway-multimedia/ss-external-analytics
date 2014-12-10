<?php
/**
 * Milkyway Multimedia
 * Record.php
 *
 * @package milkywaymultimedia.com.au
 * @author  Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Listeners;

use Debug;
use Milkyway\SS\ExternalAnalytics\Utilities;
use SS_HTTPResponse;

class RecordViaGoogleAnalytics
{
	const VERSION = 1;

	/* @var string */
	public $url = 'https://ssl.google-analytics.com/collect';

	/* @var \GuzzleHttp\ClientInterface */
	public $server;

	/* @var \Milkyway\SS\ExternalAnalytics\Config\GoogleAnalytics */
	public $config;

	public function event($params = [])
	{
		return $this->request(array_merge([
			'v'   => static::VERSION,
			'tid' => Utilities::env_value('TrackingId', null, $this->config),
			'cid' => $this->config->findClientId(),
			't'   => 'pageview',
		], $params));
	}

	protected function request($params = [])
	{
		$response = $this->server->post(
			$this->url,
			$params
		);

		$isError = (new SS_HTTPResponse($response->getBody()->getContents(), $response->getStatusCode(), $response->getReasonPhrase()))->isError();

		if($isError)
			Debug::message(sprintf('Action with url: %s came back with status code: %s', $response->getEffectiveUrl(), $response->getStatusCode()));

		return !$isError;
	}
} 