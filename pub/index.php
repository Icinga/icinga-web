<?php
/**
 * Allow XHR access from other domains (response for cors preflight requests)
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods:POST GET UPDATE");
header("Access-Control-Allow-Headers: x-requested-with");
header("Access-Control-Max-Age: 720000");
// just make sure a default timezone exists
if(!ini_get("date.timezone"))
	ini_set("date.timezone","GMT");
// +---------------------------------------------------------------------------+
// | An absolute filesystem path to the agavi/agavi.php script.                |
// +---------------------------------------------------------------------------+
require (dirname(dirname(__FILE__)). '/lib/agavi/src/agavi.php');

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to our app/config.php script.                 |
// +---------------------------------------------------------------------------+
// Not needed, we work with bootstrap events
require('../app/config.php');

// +---------------------------------------------------------------------------+
// | Initialize the framework. You may pass an environment name to this method.|
// | By default the 'development' environment sets Agavi into a debug mode.    |
// | In debug mode among other things the cache is cleaned on every request.   |
// +---------------------------------------------------------------------------+
Agavi::bootstrap('development');

// Setting the running context to web ...
AgaviConfig::set('core.default_context', 'web');

// Main module


// Some kind of agavi like bootstrap
// hook in
AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');

// +---------------------------------------------------------------------------+
// | Call the controller's dispatch method on the default context              |
// +---------------------------------------------------------------------------+

AgaviContext::getInstance('web')->getController()->dispatch();

// AppKitEventDispatcher::getInstance()->triggerSimpleEvent('agavi.afterdispatch', 'Agavi stoped, response is served');

exit (0);

?>
