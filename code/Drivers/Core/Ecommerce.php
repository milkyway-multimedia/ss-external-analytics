<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Core;

/**
 * Milkyway Multimedia
 * Create.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as DriverContract;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\DriverAttribute;

use SS_HTTPRequest as Request;
use SS_HTTPResponse as Response;
use Session;
use DataModel;

class Ecommerce implements DriverAttribute
{
    public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel)
    {
        singleton('require')->utilities_js();
        singleton('require')->add(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/ecommerce-tracker.js');

        $shop = (array)$driver->setting($id, 'EcommerceTracking', []);

        foreach ($shop as $key => $params) {
            if (!is_array($params) || (is_int($key) && !isset($params['action']))) {
                continue;
            }

            $action = is_int($key) ? $params['action'] : $key;

            $shop[$key] = $this->createTransaction($action, $params);
        }

        if (count($shop)) {
            singleton('ea')->configure('shop', $shop);
        }
    }

    public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model)
    {
        $rawShop = singleton('ea')->unqueue('ecommerce');
        $shop = [];

        foreach ($rawShop as $key => $options) {
            if (!is_array($options) || (is_int($key) && !isset($options['action']))) {
                continue;
            }

            $action = is_int($key) ? $options['action'] : $key;

            $shop[$key] = $this->createTransaction($action, $options);
        }

        if (count(array_filter($shop))) {
            singleton('ea')->configure('shop', $shop);
        }
    }

    protected function createTransaction($action = 'impression', $params = [])
    {
        return array_merge($params, [
            'action' => $action,
        ]);
    }
}