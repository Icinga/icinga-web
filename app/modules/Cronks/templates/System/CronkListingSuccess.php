<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	var panel = new Cronk.util.CronkListingPanel({
		combinedProviderUrl: '<?php echo $ro->gen("cronks.provider.combined"); ?>',
	});
	
	this.add(panel);
	
	this.doLayout.defer(500);
});
</script>
