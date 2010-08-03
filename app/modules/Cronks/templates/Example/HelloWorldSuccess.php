<script type="text/javascript">
// Start with a simple scriptblock

// This is the init method called when the cronk environment is ready
Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {
	
	var CE = this;
	
	var p = this.add({
	
		xtype: 'panel',
		
		items: [{
			layout: 'absolute',
			width: 200,
			height: 50,
			style: 'margin: 20px auto',
			border: false,
			items: [{
				xtype: 'button',
				text: _('Press me'),
				width: '100%',
				height: 50,
				iconCls: 'icinga-icon-bell',
				handler: function(btn, e) {
					Ext.MessageBox.alert(_('Say what?'), CE.getParameter('say_what'));
				}
			}]
		
		
		}]
	});
	
	this.doLayout();
});
</script>