description = Group availability for services
logfile = groups_service.log

global_vars {
	includesoftstates = 1
}

Group availability including soft states (servicegroups) {
	start_time = 1202684400
	end_time = 1202770800
	report_type = servicegroups
	objects {
		group1 {
			testhost;PING
		}
		group2 {
			testhost2;PING
		}
	}
	correct {
		TIME_OK_UNSCHEDULED = 82800
		TIME_WARNING_UNSCHEDULED = 3600
	}
}

Group availability including soft states (services) {
	start_time = 1202684400
	end_time = 1202770800
	report_type = services
	objects {
		testhost;PING
		testhost2;PING
	}
	correct {
		TIME_OK_UNSCHEDULED = 82800
		TIME_WARNING_UNSCHEDULED = 3600
	}
}

Group availability including soft states, reversed host order (services) {
	start_time = 1202684400
	end_time = 1202770800
	report_type = services
	objects {
		testhost2;PING
		testhost;PING
	}
	correct {
		TIME_OK_UNSCHEDULED = 82800
		TIME_WARNING_UNSCHEDULED = 3600
	}
}

Group availability excluding soft states (services) {
	start_time = 1202684400
	end_time = 1202770800
	includesoftstates = 0
	report_type = services
	objects {
		testhost;PING
		testhost2;PING
	}
	correct {
		TIME_OK_UNSCHEDULED = 83400
		TIME_WARNING_UNSCHEDULED = 3000
	}
}

Group availability excluding soft states, reversed host order (services) {
	start_time = 1202684400
	end_time = 1202770800
	includesoftstates = 0
	report_type = services
	objects {
		testhost2;PING
		testhost;PING
	}
	correct {
		TIME_OK_UNSCHEDULED = 83400
		TIME_WARNING_UNSCHEDULED = 3000
	}
}

Cluster mode availability including soft states (services) {
	start_time = 1202684400
	end_time = 1202770800
	sla_mode = 2
	report_type = services
	objects {
		testhost;PING
		testhost2;PING
	}
	correct {
		TIME_OK_UNSCHEDULED = 86400
	}
}

Cluster mode availability including soft states, reversed host order (services) {
	start_time = 1202684400
	end_time = 1202770800
	sla_mode = 2
	report_type = services
	objects {
		testhost2;PING
		testhost;PING
	}
	correct {
		TIME_OK_UNSCHEDULED = 86400
	}
}

Cluster mode availability excluding soft states (services) {
	start_time = 1202684400
	end_time = 1202770800
	includesoftstates = 0
	sla_mode = 2
	report_type = services
	objects {
		testhost;PING
		testhost2;PING
	}
	correct {
		TIME_OK_UNSCHEDULED = 86400
	}
}

Cluster mode availability excluding soft states, reversed host order (services) {
	start_time = 1202684400
	end_time = 1202770800
	includesoftstates = 0
	sla_mode = 2
	report_type = services
	objects {
		testhost2;PING
		testhost;PING
	}
	correct {
		TIME_OK_UNSCHEDULED = 86400
	}
}
