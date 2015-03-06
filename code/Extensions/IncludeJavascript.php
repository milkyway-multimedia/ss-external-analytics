<?php
/**
 * Milkyway Multimedia
 * Logger.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Extensions;

use Milkyway\SS\ExternalAnalytics\Utilities;
use Requirements;

class IncludeJavascript extends \Extension implements \Flushable {
	protected $cache;

	public function onAfterInit() {
		$self = $this;
		$sessionLink = singleton('Milkyway\SS\ExternalAnalytics\Controller')->Link();
		$request = $this->owner->Request;

		Utilities::execute_on_provider_list(function($config, $prefix) use($self, $sessionLink, $request) {
			$script = '';
			$cacheKey = $this->obtainCacheKey(['url' => $request ? $request->getUrl(true) : '?', 'config' => get_class($config), 'prefix' => $prefix . '-script',]);

			if(\Director::isDev() || ($request && !\Director::isDev() && !($script = $this->cache()->load($cacheKey)))) {
				if($script = $config->javascript($self->owner, ['Var' => '__' . $prefix, 'SessionLink' => $sessionLink, 'Attributes' => $this->analyticAttributes(array_merge((array)Utilities::env_value('attributes', null, $config), (array)Utilities::env_value('attributes', $self->owner, $config)), $config, $prefix)])) {
					if($request && !\Director::isDev()) {
						require_once(THIRDPARTY_PATH . DIRECTORY_SEPARATOR .'jsmin' . DIRECTORY_SEPARATOR . 'jsmin.php');
						increase_time_limit_to();
						$script = \JSMin::minify($script);
						$this->cache()->save($script, $cacheKey);
					}
				}
			}

			if($script)
				Requirements::insertHeadTags('<script type="text/javascript">' . $script . '</script>', $prefix . '-script');
		});
	}

	protected function analyticAttributes($params = [], $config = null, $prefix = '') {
		$output = [];

		if(!$config) $config = Utilities::settings();

		foreach(array_diff((array)Utilities::env_value('script_attributes', $this->owner, $config), (array)Utilities::env_value('disabled_script_attributes', $this->owner, $config)) as $class) {
			$output[] = \Object::create($class)->output($this->owner, $params, $config, $prefix);
		}

		$output = array_filter($output);

		$this->owner->extend('updateExternalAnalyticsAttributes', $output, $params, $config, $prefix);

		return count($output) ? trim(implode("\n", $output)) : '';
	}

	public static function flush() {
		singleton(__CLASS__)->cache()->clean();
	}

	public function cache() {
		if(!$this->cache)
			$this->cache = \SS_Cache::factory('Milkyway_SS_ExternalAnalytics_Extensions_IncludeJavascript', 'Output', ['lifetime' => 20000 * 60 * 60]);

		return $this->cache;
	}

	protected function obtainCacheKey(array $vars = []) {
		return preg_replace('/[^a-zA-Z0-9_]/', '', get_class($this) . '_' . urldecode(http_build_query($vars, '', '_')));
	}
}