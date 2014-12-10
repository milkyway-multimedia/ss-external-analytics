<?php
/**
 * Milkyway Multimedia
 * GoogleAnalytics.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics;

use Milkyway\SS\ExternalAnalytics\Config\Contract;

class Utilities {
	public static $environment = [];

	protected static $environment_variables = [
		'attributes' => 'attributes',
		'script_attributes' => 'script_attributes',
 	];

	public static function config() {
		return \Config::inst()->forClass('ExternalAnalytics');
	}

	public static function settings()
	{
		return \Injector::inst()->get('Milkyway\SS\ExternalAnalytics\Config\Contract');
	}

	public static function env_value($setting, \ViewableData $object = null, Contract $config = null, $prefix = '') {
		$prefix = $prefix ?: $config ? $config->prefix() : static::settings()->prefix();

		if($object && $object->{ucfirst($prefix) . '_' . $setting})
			return $object->{ucfirst($prefix) . '_' . $setting};

		if(!(isset(static::$environment[$prefix])))
			static::$environment[$prefix] = [];

		if(isset(static::$environment[$prefix][$setting]))
			return static::$environment[$prefix][$setting];

		$value = null;

		if(!$config)
			$config = static::settings();

		$mapping = $config->map();

		if(isset($mapping[$setting]) || in_array($setting, static::$environment_variables)) {
			$dbSetting = $setting;
			$setting = isset($mapping[$setting]) ? $mapping[$setting] : $setting;

			if($object && $object->config()->{$prefix.'_'.$setting})
				$value = $object->config()->{$prefix.'_'.$setting};

			if (!$value)
				$value = static::config()->{$prefix.'_'.$setting};

			if (!$value && \ClassInfo::exists('SiteConfig')) {
				if (\SiteConfig::current_site_config()->{ucfirst($prefix).'_'.$dbSetting}) {
					$value = \SiteConfig::current_site_config()->{ucfirst($prefix).'_'.$dbSetting};
				} elseif (\SiteConfig::config()->{$prefix.'_'.$setting}) {
					$value = \SiteConfig::config()->{$prefix.'_'.$setting};
				}
			}

			if (!$value) {
				if (getenv($prefix.'_'.$setting)) {
					$value = getenv($prefix.'_'.$setting);
				} elseif (isset($_ENV[$prefix.'_'.$setting])) {
					$value = $_ENV[$prefix.'_'.$setting];
				}
			}

			if ($value) {
				self::$environment[$prefix][$setting] = $value;
			}
		}

		return $value;
	}

	public static function execute_on_provider_list($callback) {
		foreach(array_diff((array)static::config()->providers, (array)static::config()->disabled_providers) as $key => $class) {
			$config = \Object::create($class);

			$callback($config, $key);
		}
	}
} 