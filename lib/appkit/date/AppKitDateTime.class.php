<?php

class AppKitDateTime extends DateTime {
	
	public function __construct($date, DateTimeZone $tz = null) {
		if (preg_match('@^\d+$@', $date)) {
			$date = strftime('%Y-%m-%d %H:%M:%S', $date);
		}
		
		if ($tz !== null) {
			parent::__construct($date, $tz);
		}
		else {
			parent::__construct($date);
		}
	} 	
	public function getTimestamp() {
		return $this->format('U');
	}
	
	public function setTimestamp($timestamp) {
		if (preg_match('@^\d+$@', $timestamp)) {
			parent::__construct(strftime('%Y-%m-%d %H:%M:%S', $timestamp));
		}
		else {
			throw new AppKitDateTimeException('Not a unix epoch');
		}
	}
	
}

class AppKitDateTimeException extends AppKitException {}

?>