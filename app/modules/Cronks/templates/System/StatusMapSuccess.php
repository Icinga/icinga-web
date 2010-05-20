<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
var CE = this;

AppKit.ScriptDynaLoader.startBulkMode();

AppKit.ScriptDynaLoader.on(
	'bulkfinish', function () {
		var statusMap = new JitStatusMap({
			url: "<?php echo $ro->gen('cronks.statusMap.json'); ?>",
			parentId: CE.parentid
		});
	},
	this,
	{ single : true }
);

AppKit.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.CronkTrigger')) ?>");
AppKit.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.JitStatusMap')) ?>");
});
</script>