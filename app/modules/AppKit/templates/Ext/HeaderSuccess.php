<?php
    $username = $tm->_('Guest');
    $auth = false;

    if ($us->isAuthenticated()) {
        $username = $us->getNsmUser()->givenName();
        $auth = true;
		$pref = $us->getPreferences();
     
        $pref["author_name"] = $us->getNsmUser()->user_name;
    }
	else {
		$pref = new stdClass();
	}


?>

<script type="text/javascript">
Ext.onReady(function() {
	 
	<?php if ($auth === true) { ?>
	AppKit.onReady(function() {
		AppKit.setPreferences(<?php echo json_encode($pref); ?>);
		
	});
	<?php } ?>
	
	// Default ajax timeout
	Ext.Ajax.timeout = Number(<?php echo AgaviConfig::get('modules.appkit.ajax.timeout', 120000); ?>);
    	
	(function() {
	    var pub = {};
	    var _LA = AppKit.util.Layout;

	    var navbar = new AppKit.util.AppKitNavBar({
            logoutURL:      '<?php echo $ro->gen("modules.appkit.logout", array('logout' => 1)); ?>',
            preferenceURL:  '<?php echo $ro->gen("my.preferences"); ?>',
            username:       '<?php echo $username; ?>',
            hasAuth:        <?php echo ($auth == true) ? 'true' : 'false' ?>,
            menuData:       <?php echo $t['json_menu_data']; ?> 
        }); 
        _LA.addTo(navbar,null,'north'); 
	})();
	
});
</script>
