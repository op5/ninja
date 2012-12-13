<?php defined('SYSPATH') OR die('No direct access allowed.');

class HttpApiEvent_options_core extends Report_options {

        /**
         * Means to translate options back and forth between Report_options
         * terms and HTTP API parameters. Handles both input and output translation.
         */
        static $http_api_options = array(
                'alert_types' => array(
                        'options' => array(
                                'both' => 3,
                                'host' => 1,
                                'service' => 2
                        )
                ),
                'state_types' => array(
                        'options' => array(
                                'both' => 3,
                                'hard' => 2,
                                'soft' => 1
                        )
                ),
                'host_states' => array(
                        'options' => array(
                                'all' => 7,
                                'problem' => 6,
                                'up' => 1,
                                'down' => 2,
                                'unreachable' => 4
                        )
                ),
                'service_states' => array(
                        'options' => array(
                                'all' => 15,
                                'problem' => 14,
                                'ok' => 1,
                                'warning' => 2,
                                'critical' => 4,
                                'unknown' => 8
                        )
                )
        );

	function __construct($options = false)
	{
		parent::__construct($options);

		// whitelist properties to use
		$this->properties = array_intersect_key(
			$this->properties,
			array_flip(array(
				'report_period',
				'alert_types',
				'state_types',
				'host_states',
				'service_states',
				'includesoftstates',
				'host_name',
				'service_description',
				'hostgroup',
				'servicegroup',
				'start_time',
				'end_time',
				'host_filter_status',
				'service_filter_status'
			))
		);
	}

        /**
         * Overload properties to enable input such as spelled out keys ("service")
         * instead of magic ints/bitmapped values ('3')
         *
         * @param $type string
         * @param $report_info array = false
         * @return array
         */
        protected static function discover_options($type, $input = false)
        {
                $options = array();
                if($input) {
                        $options = $input;
                } elseif($_POST) {
                        $options = $_POST;
                } elseif($_GET) {
                        $options = $_GET;
                }
                if(isset($options['start_time']) && !isset($options['end_time'])) {
                        $options['end_time'] = time();
                }
		if(isset($options['start_time']) || isset($options['end_time'])) {
			// @todo workaround a nasty bug, implement this in Report_options directly
			$options['report_period'] = 'custom';
		}
		if(isset($options['host_name'])) {
			$options['host_name'] = (array) $options['host_name'];
		}
		if(isset($options['service_description'])) {
			$options['service_description'] = (array) $options['service_description'];
		}

                // translate "all" to valid int, for example
                foreach($options as $key => $value) {
                        if(isset(self::$http_api_options[$key]) &&
                                isset(self::$http_api_options[$key]['options']) &&
                                isset(self::$http_api_options[$key]['options'][$value])
                        ) {
                                $options[$key] = self::$http_api_options[$key]['options'][$value];
                        }
                }
                return $options;
        }

        /**
         * @param $value mixed
         * @param $type string
         * @return string
         */
        function format_default($value, $type)
        {
                if($type == 'bool') {
                        return (int) $value;
                }
                if($type == 'array' || $type == 'objsel') {
                        if(empty($value)) {
                                return "[empty]";
                        }
                        return implode(", ", $value);
                }
                if($type == 'string' && !$value) {
                        return '[empty]';
                }
                if($type == 'enum') {
                        return "'$value'";
                }
                return $value;
        }

	/**
	 * Final step in the "from merlin.report_data row to API-output" process
	 *
	 * @param $row array
	 * @return array
	 */
	function to_output($row)
	{
		// transform value
		$type = $row['service_description'] ? 'service' : 'host';
		$row['event_type'] = Reports_Model::event_type_to_string($row['event_type'], $type, true);
		$row['state'] = strtolower(Current_status_Model::status_text($row['state'], true, $type));

		// rename property
		$row['in_scheduled_downtime'] = $row['downtime_depth'];

		// remove property (should not be exposed)
		unset($row['downtime_depth']);
		return $row;
	}
}