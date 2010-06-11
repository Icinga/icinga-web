<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {

	var CE = this;

	var p = new Ext.Panel({
		title: 'LAOLA',
		html: 'DAS IST EIN TEST',
		layout: 'fit'
	});
	
	CE.add(p);
	CE.doLayout();

});
</script>