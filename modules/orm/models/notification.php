<?php

require_once( dirname(__FILE__).'/base/basenotification.php' );

/**
 * Describes a single object from livestatus
 */
class Notification_Model extends BaseNotification_Model {
	/**
	 * An array containing the custom column dependencies
	 */
	static public $rewrite_columns = array(
		'state_text' => array('state','notification_type')
		);
	
	/**
	 * Create an instance of the given type. Don't call dirctly, called from *Set_Model-objects
	 */
	public function __construct($values, $prefix) {
		parent::__construct($values, $prefix);
		$this->export[] = 'state_text';
	}

	/**
	 * Get the state, as text
	 */
	public function get_state_text() {
		$state = $this->get_state();
		$notification_type = $this->get_notification_type();

		switch( $notification_type ) {
			case 0: // host
				switch( $state ) {
					case 0: return 'up';
					case 1: return 'down';
					case 2: return 'unreachable';
				}
				return 'unknown'; // should never happen

			case 1: // service
				switch( $state ) {
					case 0: return 'ok';
					case 1: return 'warning';
					case 2: return 'critical';
					case 3: return 'unknown';
				}
				return 'unknown'; // should never happen
		}
		return 'unknown'; // should never happen
	}
}