<?php

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to the agavi/agavi.php script.                |
// +---------------------------------------------------------------------------+
require (dirname(dirname(__FILE__)). '/../lib/agavi/src/agavi.php');

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to our app/config.php script.                 |
// +---------------------------------------------------------------------------+
// Not needed, we work with bootstrap events
require('../../app/config.php');

// +---------------------------------------------------------------------------+
// | Initialize the framework. You may pass an environment name to this method.|
// | By default the 'development' environment sets Agavi into a debug mode.    |
// | In debug mode among other things the cache is cleaned on every request.   |
// +---------------------------------------------------------------------------+
Agavi::bootstrap('development');

// Setting the running context to web ...
AgaviConfig::set('core.default_context', 'soap');
AgaviConfig::set('core.debug', true); //sets debug mode to on


AgaviSoapController::initializeModule('Web');
AgaviSoapController::initializeModule('AppKit');

AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');

// +---------------------------------------------------------------------------+
// | Call the controller's dispatch method on the default context              |
// +---------------------------------------------------------------------------+

// AppKitEventDispatcher::getInstance()->triggerSimpleEvent('agavi.dispatch', 'Agavi is starting its container');
$ctx = AgaviContext::getInstance('soap');
$controller = $ctx->getController();
$controller->dispatch();
// AppKitEventDispatcher::getInstance()->triggerSimpleEvent('agavi.afterdispatch', 'Agavi stoped, response is served');

exit (0);

?>
