<?php 
	$files = $t['files'];
	$default = $t['default'];
	if (!is_array($files) && !count($files)) return;
?>
Ext.onReady(function() {
	var l = {};
	<?php foreach ($files as $domain=>$json): ?>
	if (typeof(l['<?php echo $domain; ?>']) == "undefined") {
		l['<?php echo $domain; ?>'] = {};
	}
	Ext.apply(l['<?php echo $domain; ?>'], <?php echo $json[1]; ?>); 
	<?php endforeach; ?>
	
	APPKIT.lib.Gettext = new Gettext({
		domain: "<?php echo $default; ?>",
		locale_data: l
	});
	
	// Make this more global available
	window['_'] = APPKIT.lib.Gettext.gettext.createDelegate(APPKIT.lib.Gettext);
	window['_gt'] = APPKIT.lib.Gettext;
});
