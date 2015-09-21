#!/usr/bin/env php
<?php

if(PHP_SAPI !== 'cli') {
	die("This script should never be run from a browser, execute it from commmand-line instead.");
}

/**
 * [version => [existing => new]]
 */
$new_rights = array(
	0 => array(
		'hostgroup_view_all' => 'management_pack_view_all',
		'hostgroup_edit_all' => 'management_pack_edit_all',
		'hostgroup_add_delete' => 'management_pack_add_delete',
	),
	1 => array(
		'api_config' => 'api_command'
	),
	2 => array(
		'system_commands' => 'manage_trapper'
	),
	3 => array(
		'host_view_all' => 'logger_access',
		'system_commands' => array(
			'logger_configuration',
			'logger_schedule_archive_search'
		)
	),
	4 => array(
		'host_edit_all' => array(
			'host_command_acknowledge',
			'host_command_add_comment',
			'host_command_schedule_downtime',
			'host_command_check_execution',
			'host_command_event_handler',
			'host_command_flap_detection',
			'host_command_notifications',
			'host_command_obsess',
			'host_command_passive_check',
			'host_command_schedule_check',
			'host_command_send_notification'
		),
		'service_edit_all' => array(
			'service_command_acknowledge',
			'service_command_add_comment',
			'service_command_schedule_downtime',
			'service_command_check_execution',
			'service_command_event_handler',
			'service_command_flap_detection',
			'service_command_notifications',
			'service_command_obsess',
			'service_command_passive_check',
			'service_command_schedule_check',
			'service_command_send_notification'
		),
		'hostgroup_edit_all' => array(
			'hostgroup_command_schedule_downtime',
			'hostgroup_command_check_execution',
			'hostgroup_command_send_notifications'
		),
		'servicegroup_edit_all' => array(
			'servicegroup_command_schedule_downtime',
			'servicegroup_command_check_execution',
			'servicegroup_command_send_notifications'
		)
	)
);



require_once('op5/config.php');
$c = new op5config();
$config = $c->getConfig('auth');
$groups = $c->getConfig('auth_groups');

foreach ($groups as &$group) {
	for ($i = isset($config['common']['version'])?$config['common']['version']:0; $i < count($new_rights); $i++) {
		foreach ($new_rights[$i] as $from => $to) {
			if (in_array($from, $group)) {
				if (is_array($to)) {
					foreach ($to as $perm) {
						$group[] = $perm;
					}
				} else {
					$group[] = $to;
				}
			}
		}
	}
}
$config['common']['version'] = $i;

$c->setConfig('auth', $config);
$c->setConfig('auth_groups', $groups);