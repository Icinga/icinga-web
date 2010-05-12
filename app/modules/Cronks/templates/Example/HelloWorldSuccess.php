<script type="text/javascript">
// Start with a simple scriptblock

// This is the init method called when the cronk environment is ready
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	
//	console.log(this);
	
	var p = this.add({
	
		xtype: 'panel',
		layout: 'fit',
		bodyStyle: 'padding: 20px 20px',
		
		defaults: {
			border: false
		},
		
		items: [{
			layout: 'border',
			
			items: [{
				width: 250,
				layout: 'fit',
				region: 'west',
				title: 'Misc',
				collapsible: true,
				
				id: this.cmpid,
				
				stateful: true,
				stateId: this.stateid,
				
				bodyStyle: 'padding: 5px 5px',
				
				items: [{
					border: false,
					
					defaults: {
                        xtype: 'button',
                        scale: 'large',
                        width: '100%',
                        iconAlign: 'left',
                        style: 'margin: 2px 0'
                    },
					
					items: [{
	                    iconCls: 'silk-bell',
	                    text: 'Button 1'
	                },{
	                    iconCls: 'silk-bell',
	                    text: 'Button 2'
	                },{
	                	iconCls: 'silk-bell',
	                    text: 'Button 3'
	                }]
					
				}]
				
			}, {
				region: 'center',
				title: 'Content'
			}]
		
		
		}]
	});
	
	this.doLayout();
});
</script>