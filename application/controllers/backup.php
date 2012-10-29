<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Backup controller
 *
 *  op5, and the op5 logo are trademarks, servicemarks, registered servicemarks
 *  or registered trademarks of op5 AB.
 *  All other trademarks, servicemarks, registered trademarks, and registered
 *  servicemarks mentioned herein may be the property of their respective owner(s).
 *  The information contained herein is provided AS IS with NO WARRANTY OF ANY
 *  KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY, AND FITNESS FOR A
 *  PARTICULAR PURPOSE.
 */
class Backup_Controller extends Authenticated_Controller {

	public $model = false;
	
	private $files2backup = array(
		'/opt/monitor/etc/nagios.cfg',
		'/opt/monitor/etc/cgi.cfg',
		'/opt/monitor/var/*.log',
		'/opt/monitor/var/status.sav',
		'/opt/monitor/var/archives',
		'/opt/monitor/var/errors',
		'/opt/monitor/var/traffic',
		'/etc/op5/*.yml',
	);
	private $asmonitor = '/usr/bin/asmonitor -q ';
	private $cmd_backup = '/opt/monitor/op5/backup/backup ';
	private $cmd_restore = '/opt/monitor/op5/backup/restore ';
	private $cmd_verify = '/opt/monitor/bin/nagios -v /opt/monitor/etc/nagios.cfg 2>/dev/null';
	private $cmd_reload = 'echo "[{TIME}] RESTART_PROGRAM;{TIME2}" >> /opt/monitor/var/rw/nagios.cmd && touch /opt/monitor/etc/misccommands.cfg';
	private $cmd_view = 'tar tfz ';

	private $backup_suffix = '.tar.gz';
	private $backups_location = '/var/www/html/backup';

	private $unauthorized = false;

	public function __construct()
	{
		parent::__construct();
		$this->template->disable_refresh = true;
		$this->auto_render = true;

		$nagioscfg = "/opt/monitor/etc/nagios.cfg";
		$handle = fopen($nagioscfg, 'r');
		while($line=fgets($handle)) {
			$cfg_file = preg_split('/^cfg_file[ \t]*=[ \t]*/', $line);
			if(isset($cfg_file[1]))
				$this->files2backup[]=trim($cfg_file[1]) . " ";
			$resource_file = preg_split('/^resource_file[ \t]*=[ \t]*/', $line);
			if(isset($resource_file[1]))
				$this->files2backup[]=trim($resource_file[1]) . " ";
			$cfg_dir = preg_split('/^cfg_dir[ \t]*=[ \t]*/', $line);
			if(isset($cfg_dir[1]))
				$this->files2backup[]=trim($cfg_dir[1]) . " ";
		}

		$user = Auth::instance()->get_user();
		if (!$user->authorized_for('configuration_information') || !$user->authorized_for('system_commands')) {

			$this->template->content = $this->add_view('unauthorized');
			$this->template->content->error_message = _("It appears as though you aren't authorized to access the backup interface.");
			$this->template->content->error_description = _('Read the section of the documentation that deals with authentication and authorization for more information.');
			$this->unauthorized = true;
		}

	}

	public function index()
	{
		if ($this->unauthorized)
			return;
		$this->template->js_header = $this->add_view('js_header');
		$this->xtra_js[] = 'application/media/js/jquery.fancybox.min.js';
		$this->template->js_header->js = $this->xtra_js;
		$this->xtra_css[] = 'application/media/css/jquery.fancybox.css';
		$this->template->css_header = $this->add_view('css_header');
		$this->template->css_header->css = $this->xtra_css;
		$this->template->content = $this->add_view('backup/list');
		$this->template->title = _('Configuration » Backup/Restore');
		$this->template->content->suffix = $this->backup_suffix;

		$files = @scandir($this->backups_location);
		if ($files === false)
			throw new Exception('Cannot get directory contents: ' . $this->backups_location);

		$suffix_len = strlen($this->backup_suffix);
		$backupfiles = array();
		foreach ($files as $file)
			if (substr($file, -$suffix_len) == $this->backup_suffix)
				$backupfiles[] = substr($file, 0, -$suffix_len);

		rsort($backupfiles);
		$this->template->content->files = $backupfiles;
	}

	public function download($file) {
		
		$file_path = $this->backups_location . "/" . $file . ".tar.gz";
		$fp = fopen($file_path, "r");
		if ($fp === false) {
			$this->template->content = $this->add_view('backup/view');
			$this->template->message = "Couldn't create filehandle.";
			return;
		}
		$hs = headers_sent();
		header('Content-Description: File Transfer');
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$file.".tar.gz");
		header("Content-Transfer-Encoding:binary");
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_path));
		fpassthru($fp);
		fclose($fp);
		$this->index();
	}

	public function view($file)
	{
		if ($this->unauthorized)
			return;

		$this->template->content = $this->add_view('backup/view');
		$this->template->title = _('Configuration » Backup/Restore » View');
		$this->template->content->backup = $file;

		$contents = array();
		$status = 0;
		exec($this->cmd_view . $this->backups_location . '/' . $file . $this->backup_suffix . ' 2>/dev/null', $contents, $status);
		sort($contents);

		$this->template->content->files = $contents;
	}

	public function verify()
	{
		if ($this->unauthorized)
			return;

		$this->template = $this->add_view('backup/verify');

		$output = array();
		exec($this->asmonitor . $this->cmd_verify, $output, $status);
		if ($status != 0)
		{
			var_dump($output);
			die();
			$this->template->status = false;
			$this->template->message = "The current configuration is invalid";
		}
		else
		{
			$this->template->status = true;
			$this->template->message = "The current configuration is valid. Creating a backup...";
		}
	}

	public function backup()
	{
		if ($this->unauthorized)
			return;

		$this->template = $this->add_view('backup/backup');

		$file = strftime('backup-%Y-%m-%d_%H.%M');
		$output = array();
		exec($this->cmd_backup . $this->backups_location . '/' . $file . $this->backup_suffix
			. ' ' . implode(' ', $this->files2backup) . ' 2>/dev/null', $output, $status);
		$nofile = array();
		foreach ($this->files2backup as $fil) {
			if (!is_file($fil)) {
				$nofile[] = $fil;
			}
		}
		var_dump($nofile);
		if ($status != 0)
		{
			$this->template->status = false;
			$this->template->file = '';
			$this->template->message = "Could not backup the current configuration . $output";
		}
		else
		{
			$this->template->status = true;
			$this->template->file = $file;
			$this->template->message = "A backup of the current configuration has been created";
		}
	}

	public function restore($file)
	{
		if ($this->unauthorized)
			return;

		$this->template = $this->add_view('backup/restore');
		$this->template->status = false;

		$status = 0;
		$output = array();
		exec($this->cmd_restore . $this->backups_location . '/' . $file . $this->backup_suffix . ' 2>/dev/null', $output, $status);
		if ($status != 0)
		{
			$this->template->message = "Could not restore the configuration '{$file}'";
			return;
		}

		exec($this->asmonitor . $this->cmd_verify, $output, $status);
		if ($status != 0)
		{
			$this->template->message = "The configuration '{$file}' has been restored but seems to be invalid";
			return;
		}
		
		$time = time();
		$this->cmd_reload = str_replace('{TIME}', $time , $this->cmd_reload);
		$this->cmd_reload = str_replace('{TIME2}', $time + 2 , $this->cmd_reload);

		exec($this->cmd_reload . ' ' . $this->backups_location . '/' . $file . $this->backup_suffix . ' 2>/dev/null', $output, $status);
		if ($status == 0)
			$this->template->message = "Could not reload the configuration '{$file}'";
		else
		{
			$this->template->status = true;
			$this->template->message = "The configuration '{$file}' has been restored";
			foreach($this->files2backup as $onefile){
				$onefile = trim($onefile);
				if(pathinfo($onefile, PATHINFO_EXTENSION) === "cfg") {
					if(file_exists($onefile) && is_writable($onefile)) {
						exec("touch $onefile");
					}
				}
			}
		}
		return;
	}

	public function delete($file)
	{
		if ($this->unauthorized)
			return;

		$this->template = $this->add_view('backup/delete');

		$this->template->status = @unlink($this->backups_location . '/' . $file . $this->backup_suffix);
		$this->template->message = $this->template->status ? "The backup '{$file}' has been deleted"
			: "Could not delete the backup '{$file}' has been deleted";
	}
}
