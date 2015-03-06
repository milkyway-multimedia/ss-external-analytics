<?php
/**
 * Milkyway Multimedia
 * SocialInteractions.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\ScriptAttributes\GoogleAnalytics;

use Milkyway\SS\ExternalAnalytics\ScriptAttributes\Contract;

class SocialInteractions implements Contract {
    public function output($controller, $params = [], \Milkyway\SS\ExternalAnalytics\Config\Contract $config = null, $prefix = '') {
	    singleton('assets')->js_attach_to_event();
        return file_get_contents(BASE_PATH . '/' . SS_EXTERNAL_ANALYTICS_DIR . '/javascript/google-analytics.track.social.js');
    }
} 