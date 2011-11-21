<script type='text/javascript'>

Cronk.util.initEnvironment('viewport-center', function() {
	var availableLogdataRaw = <?php echo $t["availableLogs"] ?>;
	var availableLogdata = [];
	for(var i in availableLogdataRaw)
		availableLogdata.push([i]);


	var logListingDataView = new Ext.DataView({
		store: new Ext.data.ArrayStore({
			data: availableLogdata,
			autoDestroy: true,
			storeId: 'AvailableLogsStore',
			idIndex: 0,
			fields: ['Name']
		}),

		tpl: new Ext.XTemplate(
			'<tpl for=".">',
				'<div class="logEntry" id="{Name}">',
					'{Name}',
				'</div>',
			'</tpl>'
		),
		overClass: 'x-view-over',
		autoLoad:true,
		multiSelect: false,
		itemSelector: 'div.logEntry',
		emptyText: _('Seems like you have no logs'),
		listeners: {
			click: function(_dview,idx,node,e) {
				var elem = _dview.getNode(idx);
				if(!elem)
					return false;
				var logName = _dview.getRecord(elem).get('Name');
				logGrid.getStore().setBaseParam("logFile",logName);
				logGrid.getStore().load({params: {limit: 100}});
			}
		}
	});

	var logStore = new Ext.data.JsonStore({
		url: '<?php echo $ro->gen("modules.appkit.data.log") ?>',
		fields: ['Time','Message','Severity'],
		autoLoad:false,
		autoDestroy: true,
		root: 'result'
		
	});
	var logGrid = new Ext.grid.GridPanel({
		store: logStore,
		
		tbar: new Ext.PagingToolbar({
			store: logStore,
			pageSize:100,
			displayInfo:true,
			totalProperty: 'total'
		}),
		autoScroll:true,
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				width: 120,
				sortable: false
			},
			columns: [
				{id: 'Time',header:_('Time'),width:100,sortable:true,dataIndex:'Time'},
				{id: 'Message',header:_('Message'),width:400,sortable:true,dataIndex:'Message'},
				{id: 'Severity',header:_('Severity'),width:100,sortable:true,dataIndex:'Severity'}
			]
		}),
		frame: true

	});


	var logPortal = AppKit.util.Layout.addTo({
		xtype: 'panel',
		
		layout: 'border',
		border: false,
		id: 'log-container',
		defaults: {
			padding:5,
			margins: '5 5 5 5'
		},
		items: [{
			region:'center',
			xtype:'panel',
			title: 'Log',
			padding:0,
			layout:'fit',
			items: logGrid
		},{
			region: 'east',
			collapsible: true,
			layout:'fit',
			width: 200,
			title: _('Available logs'),
			autoScroll: true,
			items: logListingDataView
		}]
	
	},'center')

	AppKit.util.Layout.doLayout();

}, { run: true, extready: true });
</script>
