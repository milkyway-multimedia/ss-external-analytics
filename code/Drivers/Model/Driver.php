<?php
/**
 * Milkyway Multimedia
 * Driver.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Drivers\Model;

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as Contract;
use ClassInfo;
use SiteConfig;
use ArrayData;
use SSViewer;
use Permission;
use ViewableData;

abstract class Driver implements Contract {
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

	public function configuration($id) {
		return array_merge([
			'attributes' => [],
			'disabled_attributes' => []
		], singleton('env')->get('ExternalAnalytics.enabled.' . $id));
	}

	public function setting($id, $setting, $default = null, $params = []) {
		$callbacks = [];

		if(ClassInfo::exists('SiteConfig')) {
			$siteConfig = SiteConfig::current_site_config();

			$callbacks['SiteConfig'] = function($keyParts, $key) use($id, $setting, $siteConfig) {
				$value = $siteConfig->{strtoupper($id) . '_' . $setting};
				return $value ?: $this->getOtherDefaultForSetting($setting, $id);
			};
		}

		return singleton('env')->get(strtoupper($id) . '_' . $setting, $default, array_merge([
			'beforeConfigNamespaceCheckCallbacks' => $callbacks,
			'mapping' => $this->db_to_environment_mapping($id),
		], $params));
	}

	protected function renderWithTemplate($id, ViewableData $controller = null, $params = []) {
		$params = array_merge(
			(array)$this->configuration($id),
			[
				'Var' => $id,
				'Attributes' => $this->attributes($id, $controller, $params),
			],
			$params
		);

		if ($this->template && !is_array($this->template) && substr($this->template, -3) === '.js') {
			if (isset($_GET['showtemplate']) && $_GET['showtemplate'] && Permission::check('ADMIN')) {
				$lines = file($this->template);
				echo "<h2>Template: $this->template</h2>";
				echo "<pre>";
				foreach ($lines as $num => $line) {
					echo str_pad($num + 1, 5) . htmlentities($line, ENT_COMPAT, 'UTF-8');
				}
				echo "</pre>";
			}

			$template = SSViewer::execute_string(file_get_contents($this->template), ArrayData::create($params));
		} else
			$template = ArrayData::create($params)->renderWith($this->template);

		$params = array_filter($params, function($value) {
			return is_string($value) || is_numeric($value);
		});

		return str_replace(array_map(function($key) {
			return '{{ ' . strtolower($key) . ' }}';
		}, array_keys($params)), $params, $template);
	}

	protected function attributes($id, ViewableData $controller = null, $params = []) {
		$output = [];

		foreach(array_diff(
			        (array)$this->configuration($id)['attributes'],
			        (array)$this->configuration($id)['disabled_attributes'],
			        (array)$this->setting($id, 'DisabledScriptAttributes')
		        ) as $class) {
			$output[] = \Object::create($class)->output($this, $id, $controller, $params);
		}

		$output = array_filter($output);

		if($controller)
			$controller->extend('updateExternalAnalyticsAttributes', $output, $this, $id, $controller, $params);

		return count($output) ? trim(implode("\n", $output)) : '';
	}

	protected function prependId($content, $id) {
		return strtoupper($id) . '_' . $content;
	}

	protected function getOtherDefaultForSetting($setting, $id) {
		return null;
	}
}