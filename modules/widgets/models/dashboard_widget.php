<?php


/**
 * Autogenerated class Dashboard_Widget_Model
 *
 * @todo: documentation
 */
class Dashboard_Widget_Model extends BaseDashboard_Widget_Model {

	/**
	 * For backward compatibility, get friendly name from widget
	 *
	 * @return string
	 */
	public function get_friendly_name() {
		$metadata = $this->build()->get_metadata();
		if(isset($metadata['friendly_name']))
			return $metadata['friendly_name'];
		return 'Unknown';
	}

	/**
	 * Settings is stored as a json block in database, decode and encode
	 * @see BaseDashboard_Widget_Model::get_setting()
	 *
	 * @return array
	 */
	public function get_setting() {
		$var = json_decode(parent::get_setting(), true);
		if(!is_array($var)) {
			return array();
		}
		return $var;
	}

	public function set_setting($value) {
		if (isset($value['title']) && strlen($value['title']) === 0)
			unset($value['title']);
		return parent::set_setting(json_encode($value, JSON_FORCE_OBJECT));
	}

	/**
	 * positions is stored as a json block in database, decode and encode
	 * @see BaseDashboard_Widget_Model::get_position()
	 *
	 * @return array ['c' => int, 'p' => int] where c is a cell and p is
	 * the order within the cell
	 */
	public function get_position() {
		$var = json_decode(parent::get_position(), true);
		if(!is_array($var)) {
			return array('c' => 0, 'p' => 0);
		}
		return $var;
	}

	public function set_position($value) {
		if (isset($value['title']) && strlen($value['title']) === 0)
			unset($value['title']);
		return parent::set_position(json_encode($value, JSON_FORCE_OBJECT));
	}
}
