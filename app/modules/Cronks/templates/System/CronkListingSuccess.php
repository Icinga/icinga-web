<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	var panel = new Icinga.Cronks.System.CronkListingPanel({
        combinedProviderUrl: '<?php echo $ro->gen("modules.cronks.provider.combined"); ?>',
        id: 'cronk-listing-panel',
        stateId: 'cronk-listing-panel',
        customCronkCredential: <?php echo json_encode((boolean)$us->hasCredential('icinga.cronk.custom')); ?>
	});

	<?php if ($us->hasCredential('icinga.cronk.category.admin')) { ?>
		panel.setCategoryAdmin(true);
	<?php } ?>
	
	this.add(panel);
	
	this.doLayout.defer(500);
});
</script>
