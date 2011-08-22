Ext.ns('Icinga.Cronks.System.StatusOverall');

Icinga.Cronks.System.StatusOverall.renderer = {
	prepareData: function(data, recordIndex, record) {
			data.state_org = data.state;
			
			if (data.count == 0) {
				data.state = Icinga.StatusData.wrapElement(data.type, data.state, data.count + ' {0}', Icinga.DEFAULTS.STATUS_DATA.servicestatusClass[100]);
			}
			else {
				data.state = Icinga.StatusData.wrapElement(data.type, data.state, data.count + ' {0}');
			}
			
			return data;
		}
}

Icinga.Cronks.System.StatusOverall.Cronk = Ext.extend(Ext.Panel, {
	layout : 'fit',

	
	constructor : function(config) {
		Icinga.Cronks.System.StatusOverall.Cronk.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.System.StatusOverall.Cronk.superclass.initComponent.call(this);
		
		this.refreshTask = {
			run : (function() { this.dataStore.reload(); AppKit.log(this.dataStore); }).createDelegate(this),
			interval : (1000*this.refreshTime)
		}
		
		AppKit.getTr().start(this.refreshTask);
		
		this.statusXTemplate = new Ext.XTemplate(
			'<div class="icinga-overall-status-container clearfix">',
			'<tpl for=".">',
				'<tpl if="id==1">',
				'<div class="icinga-overall-status-icon icinga-icon-host" title="' + _('Hosts') + '"></div>',
				'</tpl>',
				'<tpl if="id==6">',
				'<div class="x-clear icinga-overall-status-spacer"></div>',
				'<div class="icinga-overall-status-icon icinga-icon-service" title="' + _('Services') + '"></div>',
				'</tpl>',
				'<div class="icinga-overall-status-item" id="overall-status-{id}">',
				'<span>{state}</span>',
				'</div>',
			'</tpl>',
			'</div>'
			// ,
			// '<div class="x-clear"></div>'
		);
		
		this.dataStore = new Ext.data.JsonStore({
			url: this.providerUrl,
			storeId: 'overall-status-store'
		});
		
		this.dataView = new Ext.DataView({
			store : this.dataStore,
			prepareData : Icinga.Cronks.System.StatusOverall.renderer.prepareData,
			itemSelector:'div.icinga-overall-status-item',
			tpl : this.statusXTemplate,
			
			openCronkFn: new Ext.util.DelayedTask(function(cronk,filter) {
			    Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
			}),
			
			listeners: {
				click: function(dview, index, node, e) {
					var d = dview.getStore().getAt(index).data;

					var params = {
						template: 'icinga-' + d.type + '-template',
						action: 'System.ViewProc',
						module: 'Cronks' 
					};

					var filter = {};

					// 100 is the summary of all (== no filter)
					if (d.state_org < 99) {
						// state ok
						filter['f[' + d.type + '_status-value]'] = d.state_org;
						filter['f[' + d.type + '_status-operator]'] = 50;	
						// not pending
						filter['f['+ d.type +'_is_pending-value]'] = 0;
						filter['f['+ d.type +'_is_pending-operator]'] = 80;
					} else if (d.state_org == 99) {	// check pending
						// state ok
						filter['f[' + d.type + '_status-value]'] = 0;
						filter['f[' + d.type + '_status-operator]'] = 50;
						// pending
						filter['f['+ d.type +'_is_pending-value]'] = 0;
						filter['f['+ d.type +'_is_pending-operator]'] = 71;
					}

					var id = 'status-overall-grid' + d.type + '-' + d.state_org;

					var cronk = {
						parentid: id,
						id: id + '-frame',
						stateuid: id + '-persistent',
						title: String.format('{0}s {1}', d.type, Icinga.StatusData.simpleText(d.type, d.state_org).toLowerCase()),
						crname: 'gridProc',
						closable: true,	
						params: params
					};

					dview.openCronkFn.delay(0,null,null,[cronk, filter]);
				}
			}
		});
		
		this.add(this.dataView);
		
		this.doLayout();
	}
});