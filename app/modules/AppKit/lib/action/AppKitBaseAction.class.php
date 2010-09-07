<?php

/**
 * The base action from which all AppKit module actions inherit.
 */
class AppKitBaseAction extends IcingaBaseAction {
	
	protected function log($arg1) {
		$args = func_get_args();
		return AppKitAgaviUtil::log($args);
	}
	
}

?>
