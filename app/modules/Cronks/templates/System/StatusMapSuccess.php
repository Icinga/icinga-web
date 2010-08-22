<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
var CE = this;

AppKit.ScriptDynaLoader.startBulkMode();

AppKit.ScriptDynaLoader.on(
	'bulkfinish', function () {
		var tryToDrawStatusMap = function() {
			try {
				if(Ext.isDefined(JitStatusMap)) 
					drawMap();	
				else
					tryToDrawStatusMap.defer(200);
			} catch(e) {
				tryToDrawStatusMap.defer(200);
			}
		}
		var drawMap = function() {
			var statusMap = new JitStatusMap({
				url: "<?php echo $ro->gen('cronks.statusMap.json'); ?>",
				parentId: CE.parentid
			});
		}
		tryToDrawStatusMap();
	},
	this,
	{ single : true }
);

AppKit.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.CronkTrigger')) ?>");
AppKit.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.JitStatusMap')) ?>");
});
</script>
