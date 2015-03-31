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
use ToggleCompositeField;
use FormField;

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

		singleton('ea')->executeDrivers(function($driver, $id) use (&$db) {
			$db = array_merge($db, $driver->db($id));
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

		singleton('ea')->executeDrivers(function($driver, $id) use (&$providers, $self) {
			if(!count((array)$driver->db($id))) return;

			$fields = $driver->db($id);

			$providers[$id] = [
				'title' => $driver->title(),
				'fields' => $self->scaffoldFormFields([
					'includeRelations' => false,
					'tabbed' => false,
					'ajaxSafe' => true,
					'restrictFields' => array_keys($fields),
				]),
			];

			$providerFormFields = $providers[$id]['fields']->dataFields();

			foreach($fields as $field => $type) {
				if(isset($providerFormFields[$field]) && ($providerFormFields[$field] instanceof FormField))
					$providerFormFields[$field]->setAttribute('placeholder', $driver->setting($id, $field));
			}
		});

		if(count($providers) > 1) {
			foreach($providers as $id => $options) {
				$fields->addFieldsToTab('Root.' . $this->tab, ToggleCompositeField::create(
					$id . '_fields',
					$options['title'],
					$options['fields']
				));
			}
		}
		elseif(count($providers)) {
			$fields->addFieldsToTab('Root.' . $this->tab . '.' . $providers[0]['title'], $providers[0]['fields']);
		}
	}

	public function updateFieldLabels(&$labels) {
		singleton('ea')->executeDrivers(function($driver, $id) use (&$labels) {
			if(count((array)$driver->db($id))) {
				foreach($driver->db($id) as $field => $type) {
					$labels[$field] = FormField::name_to_label(substr($labels[$field], strlen($id . '_')));
				}
			}
		});
	}
}