<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Contracts;

/**
 * Milkyway Multimedia
 * ScriptAttribute.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use SS_HTTPRequest as Request;
use SS_HTTPResponse as Response;
use Session;
use DataModel;

interface DriverAttribute
{
    /** @return array|null */
    public function preRequest(Driver $driver, $id, Request $request, Session $session, DataModel $model);

    /** @return array|null */
    public function postRequest(Driver $driver, $id, Request $request, Response $response, DataModel $model);
} 