<?php

/**
 * The base action from which all AppKit module actions inherit.
 */
class AppKitBaseAction extends IcingaBaseAction {

    /**
     * Shortcut method to log messages easier from action
     * @param mixed $arg1
     * @see app/modules/AppKit/lib/AppKitAgaviUtil#log()
     */
    protected function log($arg1) {
        $args = func_get_args();
        return AppKitAgaviUtil::log($args);
    }

}