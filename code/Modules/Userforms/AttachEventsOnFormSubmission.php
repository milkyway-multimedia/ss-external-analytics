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
		$this->fire(true);
	}

	protected function params() {
		return array_merge([
			'category' => 'forms',
			'action' => 'submitted',
			'label' => $this->owner->Title,
			'value' => singleton('env')->get('Userforms|ExternalAnalytics.conversion_value', 1),
		], (array)$this->owner->ExternalAnalyticsParams);
	}
}