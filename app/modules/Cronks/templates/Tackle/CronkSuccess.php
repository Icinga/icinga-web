<script type="text/javascript">
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
	
	var tackleCronk = new Icinga.Cronks.Tackle.Cronk({
		
	});
	
	this.getParent().removeAll();
	
	this.add(tackleCronk);
	
	this.doLayout();
	
});
</script>