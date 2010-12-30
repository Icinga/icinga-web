<script type="text/javascript">
Cronk.util.initEnvironment('viewport-center', function() {

	AppKit.pageLoadingMask(<?php echo (int)AgaviConfig::get('modules.cronks.portal.loadmasktimeout', '1500') ?>);

	var portal = AppKit.util.Layout.addTo({
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
			style: 'height: 50px; padding: 5px; background-color: #ffffff',
			defaults: { border: false },
			autoHeight: true,

			items: [{
				xtype: 'cronk',
				crname: 'icingaSearch',
				width: 250,
				margin:0,
				border: false
			}, {
				xtype: 'cronk',
				crname: 'icingaOverallStatus',
				width: 480,
				border: false
			}, {
				xtype: 'cronk',
				crname: 'icingaMonitorPerformance',
				width: 280,
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
				border: false,
				params: {
					autoRefresh: <?php echo $us->getPrefVal('org.icinga.grid.refreshTime', AgaviConfig::get('modules.cronks.grid.refreshTime', 120)); ?>
				}
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
	if(<?php echo $rd->getParameter("isURLView") ? 1 : 0 ?>) {
		Ext.getCmp('cronk-tabs').setURLTab(<?php echo $rd->getParameter('URLData');?>);
	}
	AppKit.util.Layout.doLayout();
		
}, { run: true, extready: true });
</script>