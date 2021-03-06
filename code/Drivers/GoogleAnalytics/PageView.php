<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;

/**
 * Milkyway Multimedia
 * Create.php
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

class PageView implements DriverAttribute
{
    public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel)
    {

    }

    public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model)
    {
        $args = ['pageview'];

        if ($settings = $driver->setting($id, 'PageViewSettings', null, ['objects' => [$driver]])) {
            $args[] = $settings;
        }

        singleton('ea')->configure('GA.configuration.' . $id . '.attributes.send', [
            $args,
        ]);
    }
}