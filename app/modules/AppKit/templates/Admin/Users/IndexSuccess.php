
<script type="text/javascript">
Ext.onReady(function() {

    var userManager = new AppKit.Admin.UserManager({
        userProviderURI: '<?php echo $ro->gen("modules.appkit.data.users")?>',
        roleProviderURI: '<?php echo $ro->gen("modules.appkit.data.groups")?>',
        authTypes: <?php echo json_encode(array_keys(AgaviConfig::get("modules.appkit.auth.provider"))); ?>,
        availablePrincipals: <?php echo json_encode($t['principals']); ?>
    });
    AppKit.util.Layout.getCenter().add(userManager);
  	AppKit.util.Layout.doLayout();
});
</script>