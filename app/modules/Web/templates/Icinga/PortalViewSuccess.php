<script type="text/javascript">
/**
 * This create the complete view (simply loading a cronk which is doing for us)
 */
Ext.onReady(function() {
	
	var t = Cronk.factory({
		crname: 'crportal',
	});
	
	AppKit.Layout.addCenter(t, true);
})
</script>