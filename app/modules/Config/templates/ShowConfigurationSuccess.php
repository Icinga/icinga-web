<?php
    $providerUrl = $ro->gen('modules.config.provider.configuration');
?>
<script type="text/javascript">

/*
 * Initializing of config cronk (Icinga.Config.Cronks.Configuration)
 */
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
    var viewer = new Icinga.Configuration.Cronks.Viewer({
        providerUrl: '<?php echo $providerUrl; ?>'
    });
    
    // Better to remote all existing components
    // to avoid errors
    this.getParent().removeAll();
    
    this.add(viewer);
    
    this.doLayout();
});
</script>
