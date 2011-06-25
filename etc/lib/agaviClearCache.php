<?php

require_once(dirname(__FILE__)."/agaviConsoleTask.php");

class agaviClearCache extends agaviConsoleTask {
    
    public function main() {
        parent::main();
        AgaviToolkit::clearCache();
    }
    
}