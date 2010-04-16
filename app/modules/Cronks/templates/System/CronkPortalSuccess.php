<?php 
	$parentid = $rd->getParameter('parentid');
?>
<script type="text/javascript">
Ext.onReady(function() {

	AppKit.Ext.pageLoadingMask();
	
	setTimeout(function() {
		AppKit.Ext.pageLoadingMask(true);
	}, 3000);

	var portal = Ext.create({
		xtype: 'panel',
		
		layout: 'border',
		border: false,
		id: 'view-container',
		
		defaults: { border: false },
		
		items: [{
			region: 'north',
			id: 'north-frame',
			layout: 'column',
			
			items: [{
				xtype: 'cronk',
				crname: 'icingaSearch',
				width: 250,
			}, {
				xtype: 'cronk',
				crname: 'icingaStatusSummary',
				width: 380,
				params: { otype: 'chart' }
			}, {
				xtype: 'cronk',
				crname: 'icingaStatusSummary',
				columnWidth: .8,
				params: { otype: 'text' }
			}]
		}, {
			region: 'south',
			id: 'south-frame',
			layout: 'fit',
			title: _('log'),
			collapsible: true,
			split: true,
			minSize: 150,
			height: 150,
			stateful: true,
			stateId: 'south-frame',
			items: {
				xtype: 'cronk',
				crname: 'gridLogView'
			}
		}, {
			region: 'center',
			id: 'center-frame',
			layout: 'fit',
			items: {
				xtype: 'cronk-tabpanel',
				plugins: 'cronk-tabhelper'
			},
			border: true,
			margins: '0 5 0 0'
		}, {
			region: 'west',
			id: 'west-frame',
			layout: 'fit',
	        split: true,
	        minSize: 200,
	        maxSize: 400,
	        width: 200,
	        collapsible: true,
	        stateful: true,
	        border: true,
			stateId: 'west-frame',
			margins: '0 0 0 5',
			items: {
				xtype: 'cronk',
				crname: 'crlist'
			}
		}]
		
	});
	
	Ext.getCmp('<?echo $parentid; ?>').add(portal);
});
</script>
