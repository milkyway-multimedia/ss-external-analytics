<?php namespace Milkyway\SS\ExternalAnalytics\Api;

/**
 * Milkyway Multimedia
 * JsConfiguration.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\DriverAttribute;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Initiates;
use RequestFilter;
use SS_HTTPRequest as Request;
use SS_HTTPResponse as Response;
use Session;
use DataModel;
use Config;
use Director;

class JsonConfiguration implements RequestFilter
{
    protected $cache;
    protected $allowed = true;

    protected static $preExecuted = false;
    protected static $postExecuted = false;

    public function preRequest(Request $request, Session $session, DataModel $model)
    {
        $this->allowed = $this->can($request);

        if (!$this->allowed || self::$preExecuted) {
            return;
        }

        singleton('ea')->executeDrivers(function(Driver $driver, $id) use ($request, $session, $model) {
            if($driver instanceof Initiates)
                $driver->init();

            singleton('ea')->executeDriverAttributes(
                function (DriverAttribute $attribute, $driver, $id) use ($request, $session, $model) {
                    $attribute->preRequest($driver, $id, $request, $session, $model);
                }, $driver, $id
            );
        });

        self::$preExecuted = true;
    }

    public function postRequest(Request $request, Response $response, DataModel $model)
    {
        if(self::$postExecuted) {
            return;
        }

        self::$postExecuted = true;

        if (!$this->can($request, $response)) {
            singleton('ea')->fireAllQueues();
            return;
        }

        $scripts = singleton('ea')->executeDriverAttributes(
            function (DriverAttribute $attribute, $driver, $id) use ($request, $response, $model) {
                $attribute->postRequest($driver, $id, $request, $response, $model);
            }
        );
        $scriptsLast = [];

        foreach($scripts as $key => $script) {
            if(is_array($script)) {
                if(isset($script['after']))
                    $scriptsLast[] = $script['after'];

                if(isset($script['before'])) {
                    $scripts[] = $script['before'];
                }

                unset($scripts[$key]);
            }
        }

        $configuration = singleton('ea')->configuration();

        if (!count($configuration) || (isset($configuration['disabled']) && $configuration['disabled'])) {
            return;
        }

        array_unshift($scripts, 'var EA =' . json_encode($configuration) . ';');

        $placeBefore = singleton('env')->get('ExternalAnalytics.place_before', '<title>');

        $response->setBody(str_replace(
            $placeBefore,
            '<script type="text/javascript" id="ea.configuration.start">' .
            implode('', $scripts) .
            '</script>' .
            $placeBefore,
            $response->getBody()
        ));

        if(count($scriptsLast)) {
            $placeEnd = singleton('env')->get('ExternalAnalytics.place_end', '</body>');

            $response->setBody(str_replace(
                $placeEnd,
                '<script type="text/javascript" id="ea.configuration.end">' .
                implode('', $scriptsLast) .
                '</script>' .
                $placeEnd,
                $response->getBody()
            ));
        }
    }

    protected function can(Request $request, Response $response = null)
    {
        if (!$this->allowed) {
            return false;
        }

        if (Director::is_cli())
            return false;

        if (!singleton('env')->get('ExternalAnalytics.allowed_media', false) && $request->isMedia()) {
            return true;
        }

        if (!singleton('env')->get('ExternalAnalytics.allowed_ajax', false) && $request->isAjax()) {
            return false;
        }

        if (!in_array($request->httpMethod(), singleton('env')->get('ExternalAnalytics.allowed_request_methods', [
            'GET',
        ]))
        ) {
            return false;
        }

        $disallowedUrls = singleton('env')->get('ExternalAnalytics.disallowed_urls', [
            '/^(' . Config::inst()->get('AdminRootController', 'url_base') . '|dev)/',
        ]);
        $url = $request->getUrl(true);

        foreach($disallowedUrls as $disallowedUrl) {
            if(preg_match($disallowedUrl, $url) === 1)
                return false;
        }

        if (!$response) {
            return true;
        }

        if ($response->isFinished() || !$response->getBody()) {
            return false;
        }

        $allowedResponseHeaders = singleton('env')->get('ExternalAnalytics.allowed_response_headers', [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);

        foreach ($allowedResponseHeaders as $name => $value) {
            if (!in_array($response->getHeader($name), (array)$value)) {
                return false;
            }
        }

        return true;
    }
}