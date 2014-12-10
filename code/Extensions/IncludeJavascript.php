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

class IncludeJavascript extends \Extension {
	public function onAfterInit() {
		$self = $this;
		$sessionLink = singleton('Milkyway\SS\ExternalAnalytics\Controller')->Link();

		Utilities::execute_on_provider_list(function($config, $prefix) use($self, $sessionLink) {
			if($script = $config->javascript($self->owner, ['Var' => '__' . $prefix, 'SessionLink' => $sessionLink, 'Attributes' => $this->analyticAttributes(array_merge((array)Utilities::env_value('attributes', null, $config), (array)Utilities::env_value('attributes', $self->owner, $config)), $config, $prefix)]))
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
}