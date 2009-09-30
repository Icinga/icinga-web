<?php

// This is deprecated, look into PortalViewSuccess for the real action

/*
<div id="icinga-cronk-portal-frame"></div>
<script type="text/javascript">
Ext.onReady(function() {

	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	
	if (Ext.get('navigationLeft')) {
		Ext.get('navigationLeft').remove();
		Ext.get('contentArea').setStyle('margin-left', 0);
	}
	
	var ele = Ext.get('icinga-cronk-portal-frame').load({
		url:		'<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'crportal')); ?>',
		params:		{ 'p[htmlid]': 'icinga-cronk-portal' },
		scripts:	true
	});

});
</script>
*/

?>