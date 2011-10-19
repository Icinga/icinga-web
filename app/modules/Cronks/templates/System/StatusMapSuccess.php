<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	var map = new JitStatusMap({
		url: "<?php echo $ro->gen('modules.cronks.statusMap.json'); ?>",
		parentId: this.parentid
	});
	
	// Link some object to the cronk registry object
	// this.getRegistryEntry().params.jitStatusmap = map;
	
	this.registry.local.statusmap = map;
});
</script>
