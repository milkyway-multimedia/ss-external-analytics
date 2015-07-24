<?php namespace Milkyway\SS\ExternalAnalytics\Listeners;
/**
 * Milkyway Multimedia
 * HttpListener.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use SS_HTTPResponse as Response;
use Debug;


abstract class HttpListener {
	/* @var string */
	public $url;

	/* @var \GuzzleHttp\ClientInterface */
	public $server;

	protected function request($params = [], $type = 'post')
	{
		$response = $this->server->$type(
			$this->url(),
			$params
		);

		$isError = (new Response($response->getBody()->getContents(), $response->getStatusCode(), $response->getReasonPhrase()))->isError();

		if((new Response($response->getBody()->getContents(), $response->getStatusCode(), $response->getReasonPhrase()))->isError()) {
			Debug::message(sprintf('Action with url: %s came back with status code: %s', $response->getEffectiveUrl(),
				$response->getStatusCode()));
		}

		return !$isError;
	}

	protected function url() {
		return $this->url;
	}
}