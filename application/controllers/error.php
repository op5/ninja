<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Error controller.
 * Show errors like 404 etc
 *
 *  op5, and the op5 logo are trademarks, servicemarks, registered servicemarks
 *  or registered trademarks of op5 AB.
 *  All other trademarks, servicemarks, registered trademarks, and registered
 *  servicemarks mentioned herein may be the property of their respective owner(s).
 *  The information contained herein is provided AS IS with NO WARRANTY OF ANY
 *  KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY, AND FITNESS FOR A
 *  PARTICULAR PURPOSE.
 */
class Error_Controller extends Ninja_Controller  {
	public function __construct()
	{
		try {
			parent::__construct();
		} catch (Exception $ex) {}
	}

	public function show_403() {
		header('HTTP/1.1 403 Forbidden');
		$this->template->content = $this->add_view('403');
		$this->template->title = _('Forbidden');
	}

	public function show_404() {
		header('HTTP/1.1 404 Not Found');
		$this->template->content = $this->add_view('404');
		$this->template->title = _('Page Not Found');
	}

	public function show_livestatus($exception) {
		$this->template->content = $this->add_view('livestatus');
		$this->template->title = _('Livestatus error');
		if (!IN_PRODUCTION)
			$this->template->content->exception = $exception;
	}
}
