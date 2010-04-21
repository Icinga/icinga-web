<?php 
	$parentid = $rd->getParameter('parentid');
?>
<script type="text/javascript">
Ext.onReady(function() {

	AppKit.pageLoadingMask(2500);

	var parentcmp = Ext.getCmp('<?php echo $parentid; ?>');

	var portal = parentcmp.add({
		xtype: 'panel',
		
		layout: 'border',
		border: false,
		id: 'view-container',
		
		defaults: { border: false, layout: 'fit' },
		style: { padding: '0px 5px 0px 5px' },
		
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
				xtype: 'cronk-control-tabs',
				plugins: new Cronk.util.CronkTabHelper(),
				id : 'cronk-tabs',
				border : false,
				enableTabScroll :true,
				resizeTabs : false,
				stateful: true,
				stateId: 'cronk-tab-panel'
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
			items: {
				xtype: 'cronk',
				crname: 'crlist',
				border: false
			}
		}]
		
	});
	
	parentcmp.doLayout();
	
});
</script>
