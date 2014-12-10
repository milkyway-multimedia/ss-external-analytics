<?php
/**
 * Milkyway Multimedia
 * Contract.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\ScriptAttributes;

use \Milkyway\SS\ExternalAnalytics\Config\Contract as Config;

interface Contract {
	public function output($controller, $params = [], \Milkyway\SS\ExternalAnalytics\Config\Contract $config = null, $prefix = '');
} 