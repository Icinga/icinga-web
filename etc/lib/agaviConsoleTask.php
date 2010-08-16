<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of agaviConsoleTask
 *
 * @author mhein
 */

require_once(dirname(__FILE__). '/../../lib/agavi/src/agavi.php');

class agaviConsoleTask extends Task {

	public function main() {
		require_once(dirname(__FILE__). '/../../app/config.php');
		Agavi::bootstrap('development');
		AgaviController::initializeModule('AppKit');
		AgaviConfig::set('core.default_context', 'console');
		AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');
		AgaviContext::getInstance('console')->getController()->dispatch();
	}

}
?>
