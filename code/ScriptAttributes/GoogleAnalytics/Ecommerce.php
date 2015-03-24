<?php
/**
 * Milkyway Multimedia
 * Ecommerce.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\ScriptAttributes\GoogleAnalytics;

use Milkyway\SS\ExternalAnalytics\ScriptAttributes\Contract;

class Ecommerce implements Contract {
	public function output($controller, $params = [], \Milkyway\SS\ExternalAnalytics\Config\Contract $config = null, $prefix = '') {
		if(isset($params['ecommerce']) && $params['ecommerce'])
			return $prefix. "('require', 'ecommerce', 'ecommerce.js');\n";

		return '';
	}
} 