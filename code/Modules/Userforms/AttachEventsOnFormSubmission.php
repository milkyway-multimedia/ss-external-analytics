<?php namespace Milkyway\SS\ExternalAnalytics\Modules\Userforms;
/**
 * Milkyway Multimedia
 * Controller.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Modules\Model\FireEvent;

class AttachEventsOnFormSubmission extends FireEvent {
	public function updateEmailData() {
		$this->fire();
	}

	protected function params() {
		return array_merge([
			'category' => 'userforms',
			'action' => 'submitted',
			'label' => $this->owner->Title . '(ID: ' . $this->owner->ID . ')',
			'value' => singleton('env')->get('Userforms|ExternalAnalytics.conversion_value', 1),
		], (array)$this->owner->ExternalAnalyticsParams);
	}
}