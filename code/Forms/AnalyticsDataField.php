<?php

/**
 * Milkyway Multimedia
 * AnalyticsDataField.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class AnalyticsDataField extends FormField
{
    public $serialiseOnSave = false; // Serialise data when saving to database, or same readable

    public function Value()
    {
        return null;
    }

    function Field($properties = [])
    {
        Requirements::javascript(THIRDPARTY_DIR . '/thirdparty/jquery/jquery.min.js');
        Requirements::javascript(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/analyticsdatafield.js');

        $this->setAttribute('data-field', $this->Name);

        return '';
    }

    function dataValue()
    {
        if (!is_array($this->value)) {
            return $this->value;
        }

        $stats = $this->value;

        if ($session = Session::get('ea.site_start')) {
            $siteSession = DBField::create_field('SS_Datetime', $session);

            foreach ($stats as $type => $data) {
                $stats['siteVisitStarted'] = $siteSession->Nice();
                $stats['timeOnSite'] = $siteSession->TimeDiff();
            }
        }

        if (isset($stats['pageSession'])) {
            $pageSession = DBField::create_field('SS_Datetime', $stats['pageSession'] / 1000);
            $stats['pageEntered'] = $pageSession->Nice();
            $stats['timeOnPage'] = $pageSession->TimeDiff();
            unset($stats['pageSession']);
        }

        if ($referrer = Session::get('ea.referrer')) {
            $stats['referrer'] = DBField::create_field('Text', $referrer)->forTemplate();
        }

        $getVarsToSession = singleton('env')->get('ExternalAnalytics.get_vars_to_session');

        array_walk($getVarsToSession, function ($options, $sessionVar) use (&$stats) {
            if ($value = Session::get($sessionVar)) {
                $getVar = isset($options['get_var']) ? $options['get_var'] : $sessionVar;
                $title = isset($options['title']) ? $options['title'] : $getVar;

                $stats[$title] = $value;
            }
        });

        if ($this->serialiseOnSave) {
            return Convert::array2json($stats);
        } else {
            $output = [];
            $this->convertStatForOutput($stats, $output);
            return implode("\n", $output);
        }
    }

    protected function convertStatForOutput($stat, &$output, $type = '')
    {
        if (is_array($stat)) {
            foreach ($stat as $name => $val) {
                $name = $type ? $name . '[' . $type . ']' : $name;
                $output[] = $this->convertStatForOutput($val, $output, $name);
            }
        } else {
            $type = str_replace('-', ' ', $type);
            $value = _t('AnalyticsDataField.' . str_replace(' ', '_', strtoupper($type)),
                    ucfirst($this->name_to_label($type))) . ': ' . $stat;
            return $value;
        }
    }
} 