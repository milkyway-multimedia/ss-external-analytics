<?php
/**
 * Milkyway Multimedia
 * ManagesFields.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Drivers\Contracts;


interface ManagesFields {
	/** @return callable */
	public function fieldManager($id, $fields);
}