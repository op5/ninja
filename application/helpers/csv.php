<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Helper for CSV functionality
 */
class csv_Core
{
	/**
	 * Set HTTP headers appropriate for CSV files
	 *
	 * This is trickier than it should be, due to IE
	 */
	public static function csv_http_headers($type, $options) {
		if (headers_sent()) {
			// gosh darnit, now I can't do anything. Oh well...
			return;
		}
		$filename = $type . '.csv';
		if ($options['schedule_id']) {
			$schedule_info = Scheduled_reports_Model::get_scheduled_data($options['schedule_id']);
			if ($schedule_info)
				$filename = $schedule_info['filename'];
		}
		header("Content-disposition: attachment; filename=".$filename);
		if (isset($_SERVER['HTTP_USER_AGENT']) &&
			(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 7') || strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 8')))
		{
			header("Pragma: hack");
			header("Content-Type: application/octet-stream");
			header("Content-Transfer-Encoding: binary");
		} else {
			header("Content-type: text/csv");
		}
	}

	/**
	 * Return the fields used in an avail report
	 */
	public static function avail_fields($type)
	{
		$fields['hosts'] = array(
			'HOST_NAME',
			'TIME_UP_SCHEDULED',
			'PERCENT_TIME_UP_SCHEDULED',
			'PERCENT_KNOWN_TIME_UP_SCHEDULED',
			'TIME_UP_UNSCHEDULED',
			'PERCENT_TIME_UP_UNSCHEDULED',
			'PERCENT_KNOWN_TIME_UP_UNSCHEDULED',
			'TOTAL_TIME_UP',
			'PERCENT_TOTAL_TIME_UP',
			'PERCENT_KNOWN_TIME_UP',
			'TIME_DOWN_SCHEDULED',
			'PERCENT_TIME_DOWN_SCHEDULED',
			'PERCENT_KNOWN_TIME_DOWN_SCHEDULED',
			'TIME_DOWN_UNSCHEDULED',
			'PERCENT_TIME_DOWN_UNSCHEDULED',
			'PERCENT_KNOWN_TIME_DOWN_UNSCHEDULED',
			'TOTAL_TIME_DOWN',
			'PERCENT_TOTAL_TIME_DOWN',
			'PERCENT_KNOWN_TIME_DOWN',
			'TIME_UNREACHABLE_SCHEDULED',
			'PERCENT_TIME_UNREACHABLE_SCHEDULED',
			'PERCENT_KNOWN_TIME_UNREACHABLE_SCHEDULED',
			'TIME_UNREACHABLE_UNSCHEDULED',
			'PERCENT_TIME_UNREACHABLE_UNSCHEDULED',
			'PERCENT_KNOWN_TIME_UNREACHABLE_UNSCHEDULED',
			'TOTAL_TIME_UNREACHABLE',
			'PERCENT_TOTAL_TIME_UNREACHABLE',
			'PERCENT_KNOWN_TIME_UNREACHABLE',
			'TIME_UNDETERMINED_NOT_RUNNING',
			'PERCENT_TIME_UNDETERMINED_NOT_RUNNING',
			'TIME_UNDETERMINED_NO_DATA',
			'PERCENT_TIME_UNDETERMINED_NO_DATA',
			'TOTAL_TIME_UNDETERMINED',
			'PERCENT_TOTAL_TIME_UNDETERMINED'
		);
		$fields['services'] = array(
			'HOST_NAME',
			'SERVICE_DESCRIPTION',
			'TIME_OK_SCHEDULED',
			'PERCENT_TIME_OK_SCHEDULED',
			'PERCENT_KNOWN_TIME_OK_SCHEDULED',
			'TIME_OK_UNSCHEDULED',
			'PERCENT_TIME_OK_UNSCHEDULED',
			'PERCENT_KNOWN_TIME_OK_UNSCHEDULED',
			'TOTAL_TIME_OK',
			'PERCENT_TOTAL_TIME_OK',
			'PERCENT_KNOWN_TIME_OK',
			'TIME_WARNING_SCHEDULED',
			'PERCENT_TIME_WARNING_SCHEDULED',
			'PERCENT_KNOWN_TIME_WARNING_SCHEDULED',
			'TIME_WARNING_UNSCHEDULED',
			'PERCENT_TIME_WARNING_UNSCHEDULED',
			'PERCENT_KNOWN_TIME_WARNING_UNSCHEDULED',
			'TOTAL_TIME_WARNING',
			'PERCENT_TOTAL_TIME_WARNING',
			'PERCENT_KNOWN_TIME_WARNING',
			'TIME_UNKNOWN_SCHEDULED',
			'PERCENT_TIME_UNKNOWN_SCHEDULED',
			'PERCENT_KNOWN_TIME_UNKNOWN_SCHEDULED',
			'TIME_UNKNOWN_UNSCHEDULED',
			'PERCENT_TIME_UNKNOWN_UNSCHEDULED',
			'PERCENT_KNOWN_TIME_UNKNOWN_UNSCHEDULED',
			'TOTAL_TIME_UNKNOWN',
			'PERCENT_TOTAL_TIME_UNKNOWN',
			'PERCENT_KNOWN_TIME_UNKNOWN',
			'TIME_CRITICAL_SCHEDULED',
			'PERCENT_TIME_CRITICAL_SCHEDULED',
			'PERCENT_KNOWN_TIME_CRITICAL_SCHEDULED',
			'TIME_CRITICAL_UNSCHEDULED',
			'PERCENT_TIME_CRITICAL_UNSCHEDULED',
			'PERCENT_KNOWN_TIME_CRITICAL_UNSCHEDULED',
			'TOTAL_TIME_CRITICAL',
			'PERCENT_TOTAL_TIME_CRITICAL',
			'PERCENT_KNOWN_TIME_CRITICAL',
			'TIME_UNDETERMINED_NOT_RUNNING',
			'PERCENT_TIME_UNDETERMINED_NOT_RUNNING',
			'TIME_UNDETERMINED_NO_DATA',
			'PERCENT_TIME_UNDETERMINED_NO_DATA',
			'TOTAL_TIME_UNDETERMINED',
			'PERCENT_TOTAL_TIME_UNDETERMINED'
		);
		if (isset($fields[$type])) {
			return $fields[$type];
		}
		else {
			$ret = $fields[strpos($type, 'host') === 0 ? 'hosts' : 'services'];
			array_unshift($ret, strtoupper($type));
			return $ret;
		}
	}
}
