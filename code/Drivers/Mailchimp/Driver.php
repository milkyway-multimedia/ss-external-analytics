<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Mailchimp;

/**
 * Milkyway Multimedia
 * Driver.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Initiates;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\ManagesFields;
use Milkyway\SS\ExternalAnalytics\Drivers\Model\Driver as AbstractDriver;
use ViewableData;
use ClassInfo;
use SiteConfig;

class Driver extends AbstractDriver implements ManagesFields, Initiates
{
    public function title($id)
    {
        return _t('ExternalAnalytics.' . $this->prependId('MAILCHIMP', $id), 'Mailchimp (Goals and Ecommerce360)');
    }

    public function db($id)
    {
        return [
            $this->prependId('UUId', $id)      => 'Varchar(255)',
            $this->prependId('StoreId', $id)   => 'Varchar(255)',
            $this->prependId('StoreName', $id) => 'Varchar(255)',
        ];
    }

    public function db_to_environment_mapping($id)
    {
        return array_merge(parent::db_to_environment_mapping($id), [
            $this->prependId('ApiKey',
                $id)                      => 'Statistics_' . $id . '|ExternalAnalytics|Mailchimp|EmailCampaigns|SiteConfig.mc_api_key',
            $this->prependId('StoreId',
                $id)                      => 'Statistics_' . $id . '|ExternalAnalytics|Mailchimp|EmailCampaigns|SiteConfig.mc_store_id',
            $this->prependId('StoreName',
                $id)                      => 'Statistics_' . $id . '|ExternalAnalytics|Mailchimp|EmailCampaigns|SiteConfig.mc_store_name',
            $this->prependId('UUId', $id) => 'Statistics_' . $id . '|ExternalAnalytics|Mailchimp|SiteConfig.mc_uuid',
            $this->prependId('JavascriptTemplate',
                $id)                      => 'Statistics_' . $id . '|ExternalAnalytics|Mailchimp|SiteConfig.mc_javascript_template',
        ]);
    }

    public function fieldManager($id, $fields)
    {
        if ($uuid = $fields->fieldByName($this->prependId('UUId', $id))) {
            $uuid->setDescription(_t('ExternalAnalytics.DESC-UUID',
                '<a href="{link}" target="_blank">This UUId can be found in your Mailchimp account under Integrations, once you have enabled the Goal module</a>',
                [
                    'link' => 'https://us1.admin.mailchimp.com/account/integrations/#facebook',
                ]));
        }

        return $fields;
    }

    protected function getOtherDefaultForSetting($setting, $id)
    {
        $siteConfig = ClassInfo::exists('SiteConfig') ? SiteConfig::current_site_config() : null;

        if ($setting == 'StoreId') {
            return substr($this->setting($id, 'StoreName'), 0, 31);
        }

        if ($setting == 'StoreName') {
            $title = '';

            if ($siteConfig) {
                $title = $siteConfig->AdminName ?: $siteConfig->Title;
            }

            if (!$title) {
                $title = singleton('LeftAndMain')->ApplicationName;
            }

            return $title ?: 'Silverstripe Store';
        }

        return parent::getOtherDefaultForSetting($setting, $id);
    }

    protected $init = false;

    public function init()
    {
        if ($this->init) {
            return;
        }
        singleton('require')->utilitiesJs();
        singleton('require')->add(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/mailchimp.js');
        $this->init = true;
    }
} 