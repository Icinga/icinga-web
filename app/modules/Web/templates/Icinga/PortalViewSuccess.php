<script type="text/javascript">

/**
 * This create the complete view (simply loading a cronk which is doing for us)
 */

Ext.onReady(function() {
	
	var sPortalCronk = "crportal";
	var sRenderElement = "contentArea";

	if (Ext.get('navigationLeft')) {
		Ext.get('navigationLeft').remove();
		Ext.get('contentArea').setStyle('margin-left', 0);
	}

	if (Ext.get(sRenderElement)) {
		
		var oCA = Ext.get(sRenderElement);
		
		var oPvData = {
			url : String.format("<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'crportal')); ?>/{0}", sPortalCronk),
			scripts : true,
			params : {
				'p[parentid]' : oCA.id
			}
		};
		
		oCA.getUpdater().setDefaultUrl(oPvData);
		
		oCA.getUpdater().refresh();
	}
});

</script>