<?php defined('SYSPATH') OR die('No direct access allowed.');

require_once('op5/config.php');
require_once('op5/log.php');

/**
 * Default controller.
 * Does not require login but should display default page
 *
 *  op5, and the op5 logo are trademarks, servicemarks, registered servicemarks
 *  or registered trademarks of op5 AB.
 *  All other trademarks, servicemarks, registered trademarks, and registered
 *  servicemarks mentioned herein may be the property of their respective owner(s).
 *  The information contained herein is provided AS IS with NO WARRANTY OF ANY
 *  KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY, AND FITNESS FOR A
 *  PARTICULAR PURPOSE.
*/
class Default_Controller extends Ninja_Controller  {
	public $csrf_config = false;
	public $route_config = false;

	public function __construct()
	{
		parent::__construct();
		$this->csrf_config = Kohana::config('csrf');
		$this->route_config = Kohana::config('routes');
	}

	public function index()
	{
		if (ninja_auth::is_locked_out()) {
			return url::redirect('default/locked_out');
		}
		//$this->template-> = $this->add_view('menu');
		$this->template->title = _('Ninja');

	}

	public function show_login()
	{
		$this->template = $this->add_view('login');
		$this->template->error_msg = $this->session->get('error_msg', false);
		$this->xtra_js = array('application/media/js/jquery.js', $this->add_path('/js/login.js'));
		$this->template->auth_modules = op5auth::instance()->get_metadata('login_screen_dropdown');
		Event::run('ninja.show_login', $this);
	}

	/**
	 * Show message (stored in session and set by do_login() below)
	 * to inform that user has been locked out due to too many failed
	 * login attempts
	 */
	public function locked_out()
	{
		echo $this->session->get('error_msg');
	}
	/**
	 * Collect user input from login form, authenticate against
	 * Auth module and redirect to controller requested by user.
	 */
	public function do_login()
	{
		# check if we should allow login by GET params
		if (Kohana::config('auth.use_get_auth')
		&& array_key_exists('username', $_GET)
		&& array_key_exists('password', $_GET)) {
			$_POST['username'] = $_GET['username'];
			$_POST['password'] = $_GET['password'];
			$_POST['auth_method'] = $this->input->get('auth_method', false);
		}

		if ($_POST) {
			$post = Validation::factory($_POST);
			$post->add_rules('*', 'required');

			if(PHP_SAPI !== 'cli' && config::get('cookie.secure') && (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS'])) {
				$this->session->set_flash('error_msg', _('Ninja is configured to only allow logins through the HTTPS protocol. Try to login via HTTPS, or change the config option cookie.secure.'));
				return url::redirect('default/show_login');
			}

			# validate that we have both username and password
			if (!$post->validate() ) {
				$error_msg = _("Please supply both username and password");
				$this->session->set_flash('error_msg', $error_msg);
				return url::redirect('default/show_login');
			}

			if ($this->csrf_config['csrf_token']!='' && $this->csrf_config['active'] !== false && !csrf::valid($this->input->post($this->csrf_config['csrf_token']))) {
				$error_msg = _("CSRF tokens did not match.<br />This often happen when your browser opens cached windows (after restarting the browser, for example).<br />Try to login again.");
				$this->session->set_flash('error_msg', $error_msg);
				return url::redirect('default/show_login');
			}

			$username    = $this->input->post('username', false);
			$password    = $this->input->post('password', false);
			$auth_method = $this->input->post('auth_method', false);

			$res = ninja_auth::login_user($username, $password, $auth_method);
			if ($res !== true) {
				return url::redirect($res);
			}

			# might redirect somewhere
			Event::run('ninja.logged_in');

			$requested_uri = Session::instance()->get('requested_uri', false);
			if ($requested_uri !== false && $requested_uri == Kohana::config('routes.log_in_form')) {
				# make sure we don't end up in infinite loop
				# if user managed to request show_login
				$requested_uri = Kohana::config('routes.logged_in_default');
			}
			if ($requested_uri !== false) {
				# remove 'requested_uri' from session
				Session::instance()->delete('requested_uri');
				return url::redirect($requested_uri);
			}

			return url::redirect(Kohana::config('routes.logged_in_default'));
		}

		# trying to login without $_POST is not allowed and shouldn't
		# even happen - redirecting to default routes
		if (!isset($auth) || !$auth->logged_in()) {
			return url::redirect($this->route_config['_default']);
		} else {
			return url::redirect($this->route_config['logged_in_default']);
		}
	}

	/**
	 * Logout user, remove session and redirect
	 *
	 */
	public function logout()
	{
		Auth::instance()->logout();
		Session::instance()->destroy();
		return url::redirect('default/show_login');
	}

	/**
	 *	Display an error message about no available
	 * 	objects for a valid user. This page is used when
	 * 	we are using login through apache.
	 */
	public function no_objects()
	{
		# unset some session variables
		$this->session->delete('username');
		$this->session->delete('auth_user');
		$this->session->delete('nagios_access');
		$this->session->delete('contact_id');

		$this->template = $this->add_view('no_objects');
		$this->template->error_msg = _("You have been denied access since you aren't authorized for any objects.");
	}

	/**
	 *	Used from CLI calls to detect cli setting and
	 * 	possibly default access from config file
	 */
	public function get_cli_status()
	{
		if (PHP_SAPI !== "cli") {
			return url::redirect('default/index');
		} else {
			$this->auto_render=false;
			$cli_access =Kohana::config('config.cli_access');
			echo $cli_access;
		}
	}

	/**
	 * Accept a call from cron to look for scheduled reports to send
	 * @param string $period_str [Daily, Weekly, Monthly, downtime]
	 */
	public function cron($period_str, $timestamp = false)
	{
		if (PHP_SAPI !== "cli") {
			die("illegal call\n");
		}
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		$this->auto_render=false;
		$cli_access = Kohana::config('config.cli_access');

		if (empty($cli_access)) {
			# CLI access is turned off in config/config.php
			op5log::instance('ninja')->log('error', 'No cli access');
			exit(1);
		}

		$op5_auth = Op5Auth::factory(array('session_key' => false));
		$op5_auth->force_user(new Op5User_AlwaysAuth());

		if ($period_str === 'downtime') {
			$sd = new ScheduleDate_Model();
			$sd->schedule_downtime($timestamp);
			exit(0);
		}

		$controller = new Schedule_Controller();
		try {
			$controller->cron($period_str);
		} catch(Exception $e) {
			$this->log->log('error', $e->getMessage() . ' at ' . $e->getFile() . '@' . $e->getLine());
			exit(1);
		}
		exit(0);
	}
}
