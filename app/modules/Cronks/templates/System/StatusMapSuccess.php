<?php
	/**
	* @author Christian Doebler <christian.doebler@netways.de>
	*/
	$parentId = $rd->getParameter('parentid');
?>
<script type="text/javascript">
AppKit.ScriptDynaLoader.startBulkMode();

AppKit.ScriptDynaLoader.on(
	'bulkfinish', function () {
		var statusMap = new JitStatusMap({
			url: "<?php echo $ro->gen('cronks.statusMap.json'); ?>",
			parentId: "<?php echo $parentId; ?>"
		});
	},
	this,
	{ single : true }
);

//if (Ext.isIE) {
//	AppKit.Ext.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.JitExCanvas')) ?>");
//}

//AppKit.Ext.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.JitLib')) ?>");
AppKit.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.CronkTrigger')) ?>");
AppKit.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.JitStatusMap')) ?>");
</script>