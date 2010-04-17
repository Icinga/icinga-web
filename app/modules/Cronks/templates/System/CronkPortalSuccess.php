<?php 
	$parentid = $rd->getParameter('parentid');
?>
<script type="text/javascript">
Ext.onReady(function() {

//	AppKit.Ext.pageLoadingMask();
	
//	setTimeout(function() {
//		AppKit.Ext.pageLoadingMask(true);
//	}, 1000);

	Cronk.items.Portal = Ext.create({
		xtype: 'panel',
		
		layout: 'border',
		border: false,
		id: 'view-container',
		
		defaults: { border: false },
		
		items: [{
			region: 'north',
			id: 'north-frame',
			layout: 'column',
			defaults: { border: false },
			
			items: [{
				xtype: 'cronk',
				crname: 'icingaSearch',
				width: 250,
				border: false
			}, {
				xtype: 'cronk',
				crname: 'icingaStatusSummary',
				width: 380,
				params: { otype: 'chart' },
				border: false
			}, {
				xtype: 'cronk',
				crname: 'icingaStatusSummary',
				columnWidth: 1,
				params: { otype: 'text' },
				border: false
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
				crname: 'gridLogView',
				border: false
			}
		}, {
			region: 'center',
			id: 'center-frame',
			layout: 'fit',
			items: {
				xtype: 'cronk-tabpanel',
				plugins: new Cronk.util.CronkTabHelper
			},
			border: true,
			margins: '0 0'
		}, {
			region: 'west',
			id: 'west-frame',
			layout: 'fit',
			autoScroll: true,
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
				crname: 'crlist',
				border: false
			}
		}]
		
	});
	
	Ext.getCmp('<?echo $parentid; ?>').add(Cronk.items.Portal);
});
</script>
