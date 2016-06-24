<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Core;

/**
 * Milkyway Multimedia
 * Conversions.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as DriverContract;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\DriverAttribute;

use SS_HTTPRequest as Request;
use SS_HTTPResponse as Response;
use Session;
use DataModel;

class Conversions implements DriverAttribute
{
    public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel)
    {
        singleton('require')->utilitiesJs();
        singleton('require')->add(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/conversion-tracker.js');

        $conversions = (array)$driver->setting($id, 'ConversionTracking', []);

        foreach ($conversions as $event => $params) {
            if (!is_array($params)) {
                continue;
            }

            foreach ($params as $paramName => $vars) {
                if (!is_array($vars)) {
                    continue;
                }

                // Disable a conversion if a mission session variable is found
                if (isset($vars['check_session_var']) && !Session::get($vars['check_session_var'])) {
                    unset($conversions[$event][$paramName]);
                }
            }
        }

        if (count($conversions)) {
            singleton('ea')->configure('conversions', $conversions);
        }
    }

    public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model)
    {
        $rawConversions = singleton('ea')->unqueue('conversion');
        $conversions = [];

        foreach ($rawConversions as $options) {
            $events[] = $options;
        }

        if (count($conversions)) {
            singleton('ea')->configure('conversions', $conversions);
        }
    }
} 