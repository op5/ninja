<?php
/**
 * A mock implementation of op5Auth, which defines a hard coded user
 * "mockeduser", which is authorized to do anything.
 */
class MockAuth extends op5auth
{
	public function __construct($config) {
		$this->denied_authpoints = array();
		if (array_key_exists('denied_authpoints', $config)) {
			$this->denied_authpoints = $config['denied_authpoints'];
		}
	}
	/**
	 * Returns true if current session has access for a given authorization
	 * point, which is always for this implementation.
	 *
	 * @param $authpoint string
	 * @return boolean true if access
	 */
	public function authorized_for($authpoint) {
		$log = op5log::instance('test');
		$ret = !in_array($authpoint, $this->denied_authpoints, true);
		$log->log('debug', ($ret ? "Authorizing " : "Not authorizing ") . "access to authpoint $authpoint");
		return $ret;
	}

	/**
	 * Returns the currently logged in user, which is a fixture "mockeduser"
	 * for this implementation
	 *
	 * @return  mixed
	 */
	public function get_user() {
		$user = new op5User(array(
			"username" => "mockeduser",
			"realname" => "Mocke D. User",
			"email" => "mockeduser@op5.com",
			"authdata" => array()
		));
		return $user;
	}
}
