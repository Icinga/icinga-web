<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	var panel = new Cronk.util.CronkListingPanel({
		combinedProviderUrl: '<?php echo $ro->gen("cronks.provider.combined"); ?>',
		id: 'cronk-listing-panel',
		stateId: 'cronk-listing-panel'
	});
	
	<?php if ($us->hasCredential('icinga.cronk.category.admin')) { ?>
		panel.setCategoryAdmin(true);
	<?php } ?>
	
	this.add(panel);
	
	this.doLayout.defer(500);
});
</script>
