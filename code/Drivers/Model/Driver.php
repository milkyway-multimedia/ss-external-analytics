<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Model;

/**
 * Milkyway Multimedia
 * Driver.php
 *
 * @package milkyway-multimedia/ss-external-analytics
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as Contract;
use ClassInfo;
use SiteConfig;
use DB;

abstract class Driver implements Contract
{
    protected $template;

    public function __construct($template = '')
    {
        $this->template = $template;
    }

    public function db_to_environment_mapping($id)
    {
        return [
            $this->prependId('DisabledScriptAttributes', $id) => 'ExternalAnalytics.disabled_script_attributes',
        ];
    }

    public function configuration($id)
    {
        return array_merge([
            'attributes'          => [],
            'disabled_attributes' => [],
        ], singleton('env')->get('ExternalAnalytics.enabled.' . $id));
    }

    public function setting($id, $setting, $default = null, $params = [])
    {
        $callbacks = [];

        if (ClassInfo::exists('SiteConfig')) {
            if (!DB::isActive()) {
                global $databaseConfig;
                if ($databaseConfig) {
                    DB::connect($databaseConfig);
                }
            }

            $siteConfig = SiteConfig::current_site_config();

            $callbacks['SiteConfig'] = function ($keyParts, $key) use ($id, $setting, $siteConfig) {
                $value = $siteConfig->{strtoupper($id) . '_' . $setting};
                return $value ?: $this->getOtherDefaultForSetting($setting, $id);
            };
        }

        return singleton('env')->get(strtoupper($id) . '_' . $setting, $default, array_merge([
            'beforeConfigNamespaceCheckCallbacks' => $callbacks,
            'mapping'                             => $this->db_to_environment_mapping($id),
        ], $params));
    }

    protected function prependId($content, $id)
    {
        return strtoupper($id) . '_' . $content;
    }

    protected function getOtherDefaultForSetting($setting, $id)
    {
        return null;
    }
}