<?php
/**
 * Milkyway Multimedia
 * SiteConfig.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Extensions;

use Milkyway\SS\ExternalAnalytics\Utilities;

class HasConfig extends \DataExtension
{
    protected $tab = 'Statistics';

    public function __construct($tab = 'Statistics')
    {
        parent::__construct();
        $this->tab = $tab;
    }

	public static function get_extra_config($class, $extension, $params = []) {
		$db = [];

		Utilities::execute_on_provider_list(function($config, $prefix) use (&$db) {
			if(count((array)$config->db())) {
				foreach($config->db() as $field => $type)
					$db[ucfirst($config->prefix()) . '_' . $field] = $type;
			}
		});

		return [
			'db' => $db,
		];
	}

    function updateCMSFields(\FieldList $fields)
    {
	    $this->updateFields($fields);
    }

    function updateSettingsFields($fields)
    {
	    $this->updateFields($fields);
    }

	function updateFields($fields)
	{
		$providers = [];
		$self = $this->owner;

		Utilities::execute_on_provider_list(function($config, $prefix) use (&$providers, $self) {
			if(!count($config->db())) return;

			$dbFields = [];
			foreach($config->db() as $field => $type)
				$dbFields[$field] = ucfirst($config->prefix()) . '_' . $field;

			$providers[$prefix] = $self->scaffoldFormFields([
				'includeRelations' => false,
				'tabbed' => false,
				'ajaxSafe' => true,
				'restrictFields' => array_values($dbFields),
			]);

			$providerFormFields = $providers[$prefix]->dataFields();

			foreach($dbFields as $field => $dbField) {
				if(isset($providerFormFields[$dbField]) && ($providerFormFields[$dbField] instanceof \FormField))
					$providerFormFields[$dbField]->setAttribute('placeholder', Utilities::env_value($field));
			}
		});

		if(count($providers) > 1) {
			$tab = $this->tab;

			Utilities::execute_on_provider_list(function($config, $prefix) use ($fields, $providers, $tab) {
				if(!isset($providers[$prefix])) return;

				$fields->addFieldsToTab('Root.' . $tab, \ToggleCompositeField::create(
					$prefix . '_fields',
					$config->i18n_title(),
					$providers[$prefix]
				));
			});
		}
		elseif(count($providers)) {
			$fields->addFieldsToTab('Root.' . $this->tab, array_pop($providers));
		}
	}

	public function updateFieldLabels(&$labels) {
		Utilities::execute_on_provider_list(function($config, $prefix) use (&$labels) {
			if(count((array)$config->db())) {
				foreach($config->db() as $field => $type) {
					$labelField = ucfirst($config->prefix()) . '_' . $field;

					if(isset($labels[$labelField]) && strpos($labels[$labelField], ucfirst($config->prefix() . '_')) === 0) {
						$labels[$labelField] = isset($labels[$field]) ? $labels[$field] : substr($labels[$labelField], strlen($config->prefix() . '_'));
					}
				}
			}
		});
	}

    public function getGoogleAnalyticsTrackingID() {
        return Utilities::env_value('TrackingId', $this->owner);
    }
}