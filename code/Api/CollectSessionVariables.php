<?php namespace Milkyway\SS\ExternalAnalytics\Api;

/**
 * Milkyway Multimedia
 * CollectSessionVariables.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use RequestFilter;
use SS_HTTPRequest;
use SS_HTTPResponse;
use Session;
use DataModel;

class CollectSessionVariables implements RequestFilter
{
    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
        if (!$session->get('ea.site_start')) {
            $session->set('ea.site_start', time());
            $session->set('ea.session_started', time());
            $session->set('ea.referrer', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        } else {
            $session->clear('ea.session_started');
        }

        $session->set('ea.page_start', time());

        $getVarsToSession = singleton('env')->get('ExternalAnalytics.get_vars_to_session');

        array_walk($getVarsToSession, function ($options, $sessionVar) use ($session, $request) {
            $getVar = isset($options['get_var']) ? $options['get_var'] : $sessionVar;

            if (!$session->get($sessionVar) && $request->getVar($getVar)) {
                $session->set($sessionVar, $request->getVar($getVar));
            }
        });

        $session->save();
    }

    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {

    }
} 