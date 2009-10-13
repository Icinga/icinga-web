<script type="text/javascript">
/**
 * This create the complete view (simply loading a cronk which is doing for us)
 */
(function() {
Ext.onReady(function() {
	
	var sPortalCronk = "crportal";
	var sRenderElement = "contentArea";

	if (Ext.get(sRenderElement)) {
		
		var oCA = Ext.get(sRenderElement);
		
		oCA.getUpdater().setDefaultUrl({
			url : String.format("<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'crportal')); ?>/{0}", sPortalCronk),
			scripts : true,
			params : {
				'p[parentid]' : oCA.id
			}
		});
		
		oCA.getUpdater().refresh();
		
	}
})
})();
</script>