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
	public function afterCallActionHandler($request, $action) {
		if(!$request)
			$request = $this->owner->Request;

		if( $request &&
			(($request->isAjax() && in_array($action, ['process', 'processForm'])) ||
			(!$request->isAjax() && $action == 'finished'))
		) {
			$this->fire((bool)$this->owner->redirectedTo());
		}
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