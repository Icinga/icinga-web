<?php
    $config_name = $rd->getParameter('jasperconfig', 'modules.reporting.jasperconfig.default');
    
    $enable_onthefly = (boolean)$rd->getParameter('enable_onthefly', true);
    $enable_repository = (boolean)$rd->getParameter('enable_repository', true);
    $enable_scheduling = (boolean)$rd->getParameter('enable_scheduling', true);
    
    
    $provider_config = array('jasperconfig' => $config_name);
    $treeloader_url = $ro->gen('modules.reporting.provider.treeloader', $provider_config);
    $parampanel_url = $ro->gen('modules.reporting.provider.parameters', $provider_config);
    $creator_url = $ro->gen('modules.reporting.provider.generate', $provider_config);
    $resource_url = $ro->gen('modules.reporting.provider.content.meta', $provider_config);
    
    
?>
<script type="text/javascript">

/*
 * Initializing our reporting cronk (Reporting.Cronk.Main)
 */
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {

	var jpanel = new Icinga.Reporting.Cronk({
		jasperconfig : '<?php echo  $config_name; ?>',
		
		enable_onthefly : <?php echo  json_encode($enable_onthefly); ?>,
		enable_repository : <?php echo  json_encode($enable_repository); ?>,
		enable_scheduling : <?php echo  json_encode($enable_scheduling); ?>,
		
		treeloader_url : '<?php echo $treeloader_url; ?>',
		parampanel_url : '<?php echo $parampanel_url; ?>',
		creator_url : '<?php echo $creator_url; ?>',
		resource_url : '<?php echo $resource_url; ?>'
	});

	// Better to remote all existing components
	// to avoid errors
	this.getParent().removeAll();
	
	this.add(jpanel);
	
	this.doLayout();
});
</script>