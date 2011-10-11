<script type='text/javascript'>
Ext.onReady(function() {
    new AppKit.Admin.RoleManager({
        userProviderURI: '<?php echo $ro->gen("modules.appkit.data.users")?>',
        roleProviderURI: '<?php echo $ro->gen("modules.appkit.data.groups")?>',
        availablePrincipals: <?php echo json_encode($t['principals']); ?>
    });
  	AppKit.util.Layout.doLayout();
});
</script>