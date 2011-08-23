<script type="text/javascript">
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
	var cronk = new Icinga.Cronks.System.StatusOverall.Cronk({
		providerUrl : '<?php echo $ro->gen('modules.cronks.statusOverall.json') ?>',
		refreshTime : <?php echo (int)$us->getPrefVal('org.icinga.status.refreshTime', 60); ?>
	});
	
	this.getParent().removeAll();
	
	this.add(cronk);
	
	this.doLayout();
});
</script>
