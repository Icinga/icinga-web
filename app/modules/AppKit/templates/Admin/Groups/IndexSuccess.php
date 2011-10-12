<script type='text/javascript'>
Ext.onReady(function() {
    var roleMgr = new AppKit.Admin.RoleManager({
        userProviderURI: '<?php echo $ro->gen("modules.appkit.data.users")?>',
        roleProviderURI: '<?php echo $ro->gen("modules.appkit.data.groups")?>',
        availablePrincipals: <?php echo json_encode($t['principals']); ?>
    });
    AppKit.util.Layout.getCenter().add(roleMgr);
  	AppKit.util.Layout.doLayout();
});
</script>