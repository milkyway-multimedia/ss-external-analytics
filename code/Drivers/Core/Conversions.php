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

class Conversions implements ScriptAttribute {
    public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = []) {
        singleton('assets')->utilities_js();
        singleton('assets')->javascript(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/conversion-tracker.js');
        $trackers = (array)$driver->setting($id, 'ConversionTracking', null, [
            'objects' => [$controller],
        ]);

        foreach($trackers as $trackerName => $tracker) {
            if(!isset($tracker['track']))
                unset($trackers[$trackerName]);
        }

        return count($trackers) ? '
            var EA = window.EA || {};

            EA.conversion_trackers = ' . Convert::raw2json($trackers) . '
        ' : '';
    }
} 