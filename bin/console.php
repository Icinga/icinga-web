#!/usr/bin/php
<?php

error_reporting(E_ALL);

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to the agavi/agavi.php script.                |
// +---------------------------------------------------------------------------+
require(dirname(__FILE__) . '/../lib/agavi/src/agavi.php');

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to our app/config.php script.                 |
// +---------------------------------------------------------------------------+
require(dirname(__FILE__) . '/../app/config.php');

// +---------------------------------------------------------------------------+
// | Initialize the framework. You may pass an environment name to this method.|
// | By default the 'development' environment sets Agavi into a debug mode.    |
// | In debug mode among other things the cache is cleaned on every request.   |
// +---------------------------------------------------------------------------+
// Setting the running context to web ...
Agavi::bootstrap('development');
AgaviConfig::set('core.default_context', 'console');

AgaviController::initializeModule('AppKit');

AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');


// +---------------------------------------------------------------------------+
// | Call the controller's dispatch method on the default context              |
// +---------------------------------------------------------------------------+
AgaviContext::getInstance('console')->getController()->dispatch();

?>
