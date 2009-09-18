<?php
// Sets the appkit version
AgaviConfig::set('de.icinga.appkit.version', '1.0', true, true);
AgaviConfig::set('de.icinga.appkit.release', 'ICINGAAppKit/'. AgaviConfig::get('de.icinga.appkit.version'), true, true);
?>