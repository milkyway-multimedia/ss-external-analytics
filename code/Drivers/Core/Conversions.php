<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Core;
/**
 * Milkyway Multimedia
 * Conversions.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as DriverContract;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\ScriptAttribute;
use ViewableData;
use Convert;
use Session;

class Conversions implements ScriptAttribute {
    public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = []) {
        singleton('assets')->utilities_js();
        singleton('assets')->javascript(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/conversion-tracker.js');

        $trackers = (array)$driver->setting($id, 'ConversionTracking', null, [
            'objects' => [$controller],
        ]);

        foreach($trackers as $event => $params) {
            if(!is_array($params)) continue;

            foreach($params as $paramName => $vars) {
                if(!is_array($vars)) continue;

                // Disable a conversion if a mission session variable is found
                if(isset($vars['check_session_var']) && !Session::get($vars['check_session_var']))
                    unset($trackers[$event][$paramName]);
            }
        }

        return count($trackers) ? '
            var EA = window.EA || {};

            EA.conversion_trackers = ' . Convert::raw2json($trackers) . '
        ' : '';
    }
} 