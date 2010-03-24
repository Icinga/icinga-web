<?php 
	$files = $t['files'];
	$default = $t['default'];
	if (!is_array($files) && !count($files)) return;
?>
Ext.onReady(function() {
	var store = AppKit.Ext.Storage.getStore("i18n_data");
	<?php foreach ($files as $domain=>$json): ?>
	store.add('<?php echo $domain; ?>', <?php echo $json; ?>);
	<?php endforeach; ?>
	
	var json_locale_data = {};
	store.eachKey(function(k, v) {
		var t = {};
		t[k] = v;
		Ext.apply(json_locale_data, t)
	});
	
	AppKit.Gettext = new Gettext({
		domain: "<?php echo $default; ?>",
		locale_data: json_locale_data
	});
	
	// Make this more global available
	window._ = AppKit.Gettext.gettext.createDelegate(AppKit.Gettext);
	window._gt = AppKit.Gettext;
	
});