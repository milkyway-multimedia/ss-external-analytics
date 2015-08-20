<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;

/**
 * Milkyway Multimedia
 * Ecommerce.php
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

class DisplayFeatures implements DriverAttribute
{
    public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel)
    {
        $args = ['displayfeatures'];

        if ($settings = $driver->setting($id, 'DisplayFeatureSettings', null, ['objects' => [$driver]])) {
            $args[] = $settings;
        }

        singleton('ea')->configure('GA.configuration.' . $id . '.attributes.require', [
            $args,
        ]);
    }

    public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model)
    {

    }
}