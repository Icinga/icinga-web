<?php
/*
 * This add additional config options to module.xml to module
 * dependent configuration for routes, database, ...
 * 
 * To get the module work you have to activate the module
 * in icinga.xml
 */

AppKitModuleUtil::getInstance()->registerModule('TestDummy');
?>