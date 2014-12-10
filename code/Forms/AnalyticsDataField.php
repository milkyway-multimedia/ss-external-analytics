<?php
/**
 * Milkyway Multimedia
 * GoogleAnalyticsDataField.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class AnalyticsDataField extends FormField {
	public function Value() {
		return null;
	}

	function Field($properties = []) {
		Requirements::javascript(THIRDPARTY_DIR . '/thirdparty/jquery/jquery.min.js');
		Requirements::javascript(SS_GOOGLE_ANALYTICS_DIR . '/javascript/analyticsdatafield.js');

		$this->setAttribute('data-field', $this->Name);

		return parent::Field($properties);
	}
} 