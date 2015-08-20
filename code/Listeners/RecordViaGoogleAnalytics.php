<?php namespace Milkyway\SS\ExternalAnalytics\Listeners;

/**
 * Milkyway Multimedia
 * RecordViaGoogleAnalytics.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author  Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use League\Event\EventInterface as Event;
use Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics\Driver;

class RecordViaGoogleAnalytics extends HttpListener
{
    const VERSION = 1;

    /* @var string */
    public $url = 'http://www.google-analytics.com/collect';

    protected $mapping = [
        'version'             => 'v',
        'tracking_id'         => 'tid',
        'client_id'           => 'cid',
        'category'            => 'ec',
        'action'              => 'ea',
        'label'               => 'el',
        'value'               => 'ev',
        'eventCategory'       => 'ec',
        'eventAction'         => 'ea',
        'eventLabel'          => 'el',
        'eventValue'          => 'ev',
        'order_id'            => 'ti',
        'transaction_id'      => 'ti',
        'id'                  => 'ti',
        'affiliation'         => 'ta',
        'revenue'             => 'tr',
        'shipping'            => 'ts',
        'tax'                 => 'tt',
        'product'             => 'in',
        'name'                => 'in',
        'price'               => 'ip',
        'quantity'            => 'iq',
        'code'                => 'ic',
        'sku'                 => 'ic',
        'product_category'    => 'iv',
        'currency'            => 'cu',
        'product_action'      => 'pa',
        'product_action_list' => 'pal',
        'coupon'              => 'tcc',
        'checkout_step'       => 'cos',
        'checkout_info'       => 'col',
        'promo_action'        => 'promoa',
        'social_network'      => 'sn',
        'social_action'       => 'sa',
        'social_target'       => 'st',
    ];

    protected $actionMapping = [
        'addToCart'      => 'item',
        'removeFromCart' => 'item',
    ];

    protected $enhancedActions = [
        'item' => [
            'view'             => 'il{{i}}{{option}}',
            'id'               => 'pr{{i}}id',
            'name'             => 'pr{{i}}nm',
            'product_category' => 'pr{{i}}ca',
            'brand'            => 'pr{{i}}br',
            'variant'          => 'pr{{i}}va',
            'position'         => 'pr{{i}}ps',
        ],
    ];

    public function event(Event $e, $queueName, $params = [])
    {
        $results = [];

        singleton('ea')->executeDrivers(function ($driver, $id) use (&$results, $queueName, $params) {
            if (isset($params['_driver']) && $params['_driver'] != $id) {
                return;
            }

            if (($driver instanceof Driver) && $tid = $driver->setting($id, 'TrackingId')) {
                if (isset($params['action'])) {
                    if (isset($this->actionMapping[$params['action']])) {
                        $event = $this->actionMapping[$params['action']];
                    } else {
                        $event = $params['action'];
                    }
                } else {
                    $event = isset($params['event']) ? $params['event'] : $queueName;
                }

                foreach ($params as $type => $value) {
                    if (isset($this->mapping[$type])) {
                        $params[$this->mapping[$type]] = $value;
                        unset($params[$type]);
                    }
                }

                $results[] = $this->request([
                    'query' => array_merge([
                        'v'   => static::VERSION,
                        'tid' => $tid,
                        'cid' => $driver->session_user_id($id),
                        't'   => $event ?: 'pageview',
                    ], $params),
                ]);
            }
        });

        return $results;
    }

    public function ecommerce(Event $e, $queueName, $params = [])
    {
        return $this->event($e, $queueName, $params);
    }
} 