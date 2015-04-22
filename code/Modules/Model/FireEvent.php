<?php namespace Milkyway\SS\ExternalAnalytics\Modules\Model;
/**
 * Milkyway Multimedia
 * Controller.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Extension;

abstract class FireEvent extends Extension {
	protected function fire($recordViaServer = false) {
		singleton('ea')->queue('event', $this->params(), $this->eventId(), $this->owner, $recordViaServer);
	}

	abstract protected function params();

	protected function eventId() {
		return $this->owner->EA_Event ? $this->owner->EA_Event : get_class($this->owner) . '-' . $this->owner->ID;
	}
}