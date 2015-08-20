<?php namespace Milkyway\SS\ExternalAnalytics\Listeners;

/**
 * Milkyway Multimedia
 * RecordViaFacebook.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author  Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use League\Event\EventInterface as Event;

class RecordViaFacebook extends HttpListener
{
    public function __construct()
    {
        $this->url = singleton('env')->get('Facebook|ExternalAnalytics.facebook_conversion_url',
            'https://facebook.com/tr?ev=$id&noscript=1');
    }

    public function event(Event $e = null, $queueName = '', $params = [])
    {
        $results = [];

        if (isset($params['facebook'])) {
            $params = $params['facebook'];
        }

        $type = isset($params['type']) ? $params['type'] : $queueName;
        $id = isset($params['id']) ? $params['id'] : singleton('env')->get('Facebook.conversion_id_for_' . $type);

        if ($id) {
            $url = str_replace('$id', $id, $this->url());
            $oldUrl = $this->url;
            $this->url = $url;

            $results[] = $this->request([
                'query' => $params,
            ]);

            $this->url = $oldUrl;
        }

        return $results;
    }

    public function ecommerce(Event $e, $queueName, $params = [])
    {
        return $this->event($e, $queueName, $params);
    }

    public function conversion(Event $e, $queueName, $params = [])
    {
        return $this->event($e, $queueName, $params);
    }
} 