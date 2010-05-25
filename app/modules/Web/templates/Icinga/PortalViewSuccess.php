<script type="text/javascript">
/**
 * This create the complete view (simply loading a cronk which is doing for us)
 */
Ext.onReady(function() {
	
	var _LL = AppKit.util.Layout;
	
	_LL.addTo({
		layout: 'fit',
		crname: 'crportal',
		id: 'icinga-portal-loader',
		xtype: 'cronk'
	});
});

</script>