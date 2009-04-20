<?php

class AppKitColorUtil {
	
	public static function generateRandomHexColor($with_hash=true) {
		return sprintf('%s%02x%02x%02x', $with_hash ? '#' : null, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
	}
	
}

// If included, run the random utility
AppKitRandomUtil::initRand(); 

?>