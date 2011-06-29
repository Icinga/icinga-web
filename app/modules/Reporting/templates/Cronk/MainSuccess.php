<?php
    $config_name = $rd->getParameter('jasperconfig', 'modules.reporting.jasperconfig.default');
    $provider_config = array('jasperconfig' => $config_name);
    $treeloader_url = $ro->gen('modules.reporting.provider.treeloader', $provider_config);
    $parampanel_url = $ro->gen('modules.reporting.provider.parameters', $provider_config);
?>
<script type="text/javascript">

/*
 * Initializing our reporting cronk (Reporting.Cronk.Main)
 */
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {

	var jpanel = new Icinga.Reporting.Cronk({
		jasperconfig : '<?php echo  $config_name; ?>',
		treeloader_url : '<?php echo $treeloader_url; ?>',
		parampanel_url : '<?php echo $parampanel_url; ?>'
	});

	// Better to remote all existing components
	// to avoid errors
	this.getParent().removeAll();
	
	this.add(jpanel);
	
	this.doLayout();
});
</script>