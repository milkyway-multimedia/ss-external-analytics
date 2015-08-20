<?php namespace Milkyway\SS\ExternalAnalytics\Listeners;

/**
 * Milkyway Multimedia
 * HttpListener.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use SS_HTTPResponse as Response;
use Debug;

use Exception;

abstract class HttpListener
{
    /* @var string */
    public $url;

    /* @var \GuzzleHttp\ClientInterface */
    public $server;

    protected function request($params = [], $type = 'post')
    {
        $isError = true;

        try {
            $response = $this->server->$type(
                $this->url(),
                $params
            );

            $isError = (new Response($response->getBody()->getContents(), $response->getStatusCode(),
                $response->getReasonPhrase()))->isError();

            if ((new Response($response->getBody()->getContents(), $response->getStatusCode(),
                $response->getReasonPhrase()))->isError()
            ) {
                Debug::message(sprintf('Action with url: %s came back with status code: %s',
                    $response->getEffectiveUrl(),
                    $response->getStatusCode()));
            }
        } catch (Exception $e) {
            Debug::message($e->getMessage());
        }

        return !$isError;
    }

    protected function url()
    {
        return $this->url;
    }
}