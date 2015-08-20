<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Mailchimp;

/**
 * Milkyway Multimedia
 * Ecommerce.php
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
use Member;

class Ecommerce implements DriverAttribute
{
    public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel)
    {
        if ($apiKey = $driver->setting($id, 'ApiKey', null, ['objects' => [$driver]])) {

            $settings = $driver->setting($id, 'EcommerceSettings', [], ['objects' => [$driver]]);
            $apiKeyParts = explode('-', $apiKey);

            $config = [
//                'apikey'     => $apiKey,
                'dc'         => $driver->setting($id, 'dc', array_pop($apiKeyParts), [
                    'objects' => [$driver],
                ]),
                'store_id'   => $driver->setting($id, 'StoreId'),
                'store_name' => $driver->setting($id, 'StoreName'),
            ];

            if($session->get('mc.email_id'))
                $config['email_id'] = $session->get('mc.email_id');
            else if(Member::currentUser() && Member::currentUser()->Email) {
                $config['email'] = Member::currentUser()->Email;
            }

            if($session->get('mc.campaign_id'))
                $params['campaign_id'] = Session::get('mc.campaign_id');

            singleton('ea')->configure('MC.configuration.' . $id, array_merge($config, $settings));
        }
    }

    public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model)
    {
        // Currently Ecommerce360 does not seem to work with AJAX.
        // Hijacking the ecommerce array to still send transactions.
        $configuration = singleton('ea')->configuration();

        if(isset($configuration['shop'])) {
            foreach($configuration['shop'] as $transaction) {
                singleton('Milkyway\SS\ExternalAnalytics\Listeners\RecordViaEcommerce360')->ecommerce(null, 'transaction', $transaction);
            }
        }
    }
}