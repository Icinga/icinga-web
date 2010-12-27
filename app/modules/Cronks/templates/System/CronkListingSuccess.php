<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	var panel = new Cronk.util.CronkListingPanel({
		allProviderUrl: '<?php echo $ro->gen("cronks.provider.combined"); ?>',
	});
	this.add(panel);
	this.doLayout();
});
</script>
