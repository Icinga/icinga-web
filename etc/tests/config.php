<?php

AgaviConfig::set('core.testing_dir', realpath(dirname(__FILE__)));
AgaviConfig::set('core.app_dir', realpath(dirname(__FILE__).'/../../app/'));
AgaviConfig::set('core.root_dir', dirname(dirname(__FILE__))."/../");

?>