<?php 
	$files = $t['files'];
	$default = $t['default'];
	if (!is_array($files) && !count($files)) return;
?>
AppKit.onReady(function() {
	var l = {};
	<?php foreach ($files as $domain=>$json): ?>

	if (typeof(l['<?php echo $domain; ?>']) == "undefined") {
		l['<?php echo $domain; ?>'] = {};
	}

	Ext.apply(l['<?php echo $domain; ?>'], <?php echo $json[1]; ?>); 

	<?php endforeach; ?>
	
	AppKit.util.Gettext = new Gettext({
		domain: "<?php echo $default; ?>",
		locale_data: l
	});
	
	// Make this more global available
	window._ = AppKit.util.Gettext.gettext.createDelegate(AppKit.util.Gettext);
	window._gt = AppKit.util.Gettext;
}, window);
