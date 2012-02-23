<script type="text/javascript">

Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {
	
    var cronk = new Icinga.Cronks.System.MonitorPerformance.Cronk({
        hostThreshold: <?php echo $rd->getParameter('hostLatencyWarningThreshold',10000);?>,
        serviceThreshold: <?php echo $rd->getParameter('serviceLatencyWarningThreshold',10000);?>,
        refreshInterval: <?php echo $us->getPrefVal('org.icinga.status.refreshTime', 60); ?>,
        dataProvider: '<?php echo $ro->gen('modules.cronks.monitorPerformance.json') ?>',
        storeId: 'overall-status-store'
    });
    
    this.getParent().removeAll();
    
    this.add(cronk);
    
    this.doLayout();
});

</script>
