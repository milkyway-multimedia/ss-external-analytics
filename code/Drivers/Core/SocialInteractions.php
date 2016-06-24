<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Core;

/**
 * Milkyway Multimedia
 * SocialInteractions.php
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

class SocialInteractions implements DriverAttribute
{
    public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel)
    {
        singleton('require')->utilitiesJs();
        singleton('require')->add(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/social-tracker.js');
    }

    public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model)
    {

    }
}