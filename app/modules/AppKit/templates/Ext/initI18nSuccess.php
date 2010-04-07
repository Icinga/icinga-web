<?php 
	$files = $t['files'];
	$default = $t['default'];
	if (!is_array($files) && !count($files)) return;
?>
Ext.onReady(function() {
	var store = AppKit.Ext.Storage.getStore("i18n_data");
	<?php foreach ($files as $domain=>$json): ?>
	
	store.add('<?php echo $domain; ?>', <?php echo $json[1]; ?>);
	<?php endforeach; ?>
	
	var json_locale_data = {};
	store.eachKey(function(k, v) {
		var t = {};
		var langKey = "";
		// Finding the first matching language key to use
		Ext.iterate(v, function(lk, lv) { langKey=lk; return false; });
		t[k] = v[langKey];
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