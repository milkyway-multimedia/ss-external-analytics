<?php
/**
 * Milkyway Multimedia
 * GoogleAnalyticsDataField.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class AnalyticsDataField extends FormField {
	public $serialiseOnSave = false; // Serialise data when saving to database, or same readable

	public function Value() {
		return null;
	}

	function Field($properties = []) {
		Requirements::javascript(THIRDPARTY_DIR . '/thirdparty/jquery/jquery.min.js');
		Requirements::javascript(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/analyticsdatafield.js');

		$this->setAttribute('data-field', $this->Name);

		return parent::Field($properties);
	}

	function dataValue() {
		if(!is_array($this->value))
			return $this->value;

		$stats = $this->value;

		if($session = Session::get('ea.site_start')) {
			$sessionNice = DBField::create_field('SS_Datetime', $session);

			foreach($stats as $type => $data) {
				if(is_array($data)) {
					$stats[$type]['siteVisitStarted'] = $sessionNice->Nice();
					$stats[$type]['timeOnSite'] = $sessionNice->TimeDiff();
				}
				else {
					$stats['siteVisitStarted'] = $sessionNice->Nice();
					$stats['timeOnSite'] = $sessionNice->TimeDiff();
				}
			}
		}

		if($this->serialiseOnSave) {
			return Convert::array2json($stats);
		}
		else {
			$output = [];

			foreach($stats as $type => $data) {
				if(is_array($data)) {
					foreach($data as $name => $val) {
						if($name == 'pageSession') {
							$pageSession = DBField::create_field('SS_Datetime', $val / 1000);

							$output[] = _t('AnalyticsDataField.PAGE_ENTERED', 'Page entered') . ': ' . $pageSession->Nice();
							$output[] = _t('AnalyticsDataField.TIME_ON_PAGE', 'Time on page') . ': ' . $pageSession->TimeDiff();
						}
						else {
							$name = str_replace('-', ' ', $name);
							$output[] = _t('AnalyticsDataField.' . str_replace(' ', '_', strtoupper($name)), ucfirst($this->name_to_label($name))) . ': ' . $val;
						}
					}
				}
				else {
					if($type == 'pageSession') {
						$pageSession = DBField::create_field('SS_Datetime', $data / 1000);

						$output[] = _t('AnalyticsDataField.PAGE_ENTERED', 'Page entered') . ': ' . $pageSession->Nice();
						$output[] = _t('AnalyticsDataField.TIME_ON_PAGE', 'Time on page') . ': ' . $pageSession->TimeDiff();
					}
					else {
						$type = str_replace('-', ' ', $type);
						$output[] = _t('AnalyticsDataField.' . str_replace(' ', '_', strtoupper($type)), ucfirst($this->name_to_label($type))) . ': ' . $data;
					}
				}
			}

			return implode("\n", $output);
		}
	}
} 