<script type="text/javascript">
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
    
    var statusmap = new Icinga.Cronks.StatusMap.Cronk({
    	url: "<?php echo $ro->gen('modules.cronks.statusMap.json'); ?>",
    	refreshTime : "<?php echo $us->getPrefVal('org.icinga.status.refreshTime', 60); ?>"
    });

//    var map = new Icinga.Cronks.StatusMap.RGraph({
//        url: "<?php echo $ro->gen('modules.cronks.statusMap.json'); ?>",
//        parentId: panel.getId()
//    });

    // Link some object to the cronk registry object
    // this.getRegistryEntry().params.jitStatusmap = map;
    this.registry.local.statusmap = statusmap.getRGraph();

    this.getParent().removeAll();
    this.add(statusmap);
    this.doLayout();
});
</script>