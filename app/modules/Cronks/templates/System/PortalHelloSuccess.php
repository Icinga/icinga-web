<?php
	function _GV($item) {
		return AgaviConfig::get('de.icinga.appkit.version.'. $item);
	}
	
	$version_string = vsprintf('%d.%d.%d-%s', array_map("_GV", array('major', 'minor', 'patch', 'extension')));
	$version_date = AgaviConfig::get('de.icinga.appkit.version.releasedate');
	$copy =  AgaviConfig::get('de.icinga.appkit.version.copyright');
?>
<div style="margin: 10px auto; padding: 10px 10px;">

	<h1>Welcome to Icinga <?php echo $version_string; ?></h1>
	
	<p>Feel free to poke around and don't forget to visit the project homepage 	to post bug advisories or feature requests.</p>
	
	<p>What are Cronks? Simply put, they are widgets for the Icinga web front end - with a cooler name.</p>
	
	<p>Have fun!</p>
	
	<p><?php echo $tm->_d($version_date); ?> - <a href="http://www.icinga.org/"><?php echo $copy; ?></a></p>
	
</div>