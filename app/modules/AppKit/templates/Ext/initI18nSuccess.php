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
	
	AppKit.Gettext = new Gettext({
		domain: "<?php echo $default; ?>",
		locale_data: l
	});
	
	// Make this more global available
	window._ = AppKit.Gettext.gettext.createDelegate(AppKit.Gettext);
	window._gt = AppKit.Gettext;
	
});
