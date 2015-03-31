<?php namespace Milkyway\SS\ExternalAnalytics\Modules\Userforms;
/**
 * Milkyway Multimedia
 * Controller.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Extension;

class AttachEventsOnFormSubmission extends Extension {
	public function afterCallActionHandler($request, $action) {
		if(
			($request->isAjax() && in_array($action, ['process', 'processForm'])) ||
			(!$request->isAjax() && $action == 'finished')
		) {
			$this->fire($this->owner->redirectedTo());
		}
	}

	protected function fire($recordViaServer = false) {
		singleton('ea')->queue('event', [
			'category' => 'forms',
			'action' => 'submitted',
			'label' => $this->owner->Title,
			'value' => $this->owner->ConversionValue ?: singleton('env')->get('Userforms|ExternalAnalytics.conversion_value', 1),
		], get_class($this->owner) . '-' . $this->owner->ID, $this->owner, $recordViaServer);
	}
}