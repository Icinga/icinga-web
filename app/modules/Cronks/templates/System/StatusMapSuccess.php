<?php
	/**
	* @author Christian Doebler <christian.doebler@netways.de>
	*/
	$parentId = $rd->getParameter('parentid');
?>
<script type="text/javascript">
AppKit.Ext.ScriptDynaLoader.startBulkMode();

AppKit.Ext.ScriptDynaLoader.on(
	'bulkfinish', function () {
		var statusMap = new JitStatusMap({
			url: "<?php echo $ro->gen('icinga.cronks.statusMap.json'); ?>",
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
AppKit.Ext.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.CronkTrigger')) ?>");
AppKit.Ext.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.JitStatusMap')) ?>");
</script>