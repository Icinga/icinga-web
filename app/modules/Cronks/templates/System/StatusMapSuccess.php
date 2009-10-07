<?php
	/**
	* @author Christian Doebler <christian.doebler@netways.de>
	*/
	$parentId = $rd->getParameter('parentid');
?>
<script type="text/javascript">
AppKit.Ext.ScriptDynaLoader.loadScript({
	url: "<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.JitStatusMap')) ?>",
	callback: function() {
		var statusMap = new JitStatusMap({
			url: "/web/cronks/statusMap/json",
			parentId: "<?php echo $parentId; ?>"
		});
	}
});
</script>