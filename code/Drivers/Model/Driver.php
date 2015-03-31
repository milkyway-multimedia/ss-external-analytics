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
			strtoupper($id) . '_DisabledScriptAttributes' => 'ExternalAnalytics.disabled_script_attributes',
		];
	}

	public function configuration($id) {
		return array_merge([
			'attributes' => [],
			'disabled_attributes' => []
		], singleton('env')->get('ExternalAnalytics.enabled.' . $id));
	}

	public function setting($id, $setting, $default = null, $cache = true) {
		$callbacks = [];

		if(ClassInfo::exists('SiteConfig')) {
			$siteConfig = SiteConfig::current_site_config();

			$callbacks['SiteConfig'] = function($keyParts, $key) use($id, $setting, $siteConfig) {
				return $siteConfig->{strtoupper($id) . '_' . $setting};
			};
		}

		return singleton('env')->get(strtoupper($id) . '_' . $setting, $default, [
			'beforeConfigNamespaceCheckCallbacks' => $callbacks,
			'mapping' => $this->db_to_environment_mapping($id),
			'fromCache' => $cache,
			'doCache' => $cache,
		]);
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

			return SSViewer::execute_string(file_get_contents($this->template), ArrayData::create($params));
		} else
			return ArrayData::create($params)->renderWith($this->template);
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
}