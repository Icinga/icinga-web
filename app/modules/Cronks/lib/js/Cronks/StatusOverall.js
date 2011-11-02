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
    },
    
    prepareInstanceData : function(data, recordIndex, record) {
        
        var msg = "";
        
        if (data.id == 0) {
            msg = String.format(_('{0} OK'), data.count);
        } else {
            msg = String.format(_('{0} DOWN'), data.count);
        }
        
        if (data.count == 0) {
            data.state = Icinga.StatusData.wrapElement('service', data.id, msg, Icinga.DEFAULTS.STATUS_DATA.servicestatusClass[100]);
        } else {
            data.state = Icinga.StatusData.wrapElement('service', data.id, msg);
        }
        
        return data;
    }
}

Icinga.Cronks.System.StatusOverall.Cronk = Ext.extend(Ext.Panel, {
	layout : 'column',
    height : 50,

	
	constructor : function(config) {
		Icinga.Cronks.System.StatusOverall.Cronk.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.System.StatusOverall.Cronk.superclass.initComponent.call(this);
		
		this.refreshTask = {
			run : (function() {this.dataStore.reload();}).createDelegate(this),
			interval : (1000*this.refreshTime)
		}
		
		AppKit.getTr().start(this.refreshTask);
		
		this.statusXTemplate = new Ext.XTemplate(
			'<div class="icinga-overall-status-container clearfix">',
			'<tpl for=".">',
				'<tpl if="id==1">',
				'<div class="icinga-overall-status-icon icinga-icon-host" qtip="' + _('Hosts') + '"></div>',
				'</tpl>',
				'<tpl if="id==6">',
				'<div class="x-clear icinga-overall-status-spacer"></div>',
				'<div class="icinga-overall-status-icon icinga-icon-service" qtip="' + _('Services') + '"></div>',
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
		
        this.instanceStore = new Ext.data.ArrayStore({
            storeId: 'overall-status-instance-store',
            idIndex : 0,
            fields : [
                'id',
                'count',
                'msg'
            ]
        });
        
        /*
         * Also load the data for the instance store (sub namespace
         * in the status overall store)
         */
        this.dataStore.on('load', function(store, records, options) {
            var data = this.loadInstanceDataStore(store, this.instanceStore);
        }, this);
        
        this.instanceView = new Ext.DataView({
            store : this.instanceStore,
            itemSelector:'div.icinga-overall-status-item-instance',
            prepareData : Icinga.Cronks.System.StatusOverall.renderer.prepareInstanceData,
            
            tpl : new Ext.XTemplate(
                '<div style="margin-left: 5px;">',
                '<tpl for=".">',
                '<div>',
                '<tpl if="id==0">',
                '<div class="icinga-overall-status-icon-instance icinga-icon-application" ext:qtip="Instances running"></div>',
                '</tpl>',
                '<tpl if="id==2">',
                '<div class="icinga-overall-status-icon-instance icinga-icon-application-minus" ext:qtip="Instances down"></div>',
                '</tpl>',
                '<div class="icinga-overall-status-item icinga-overall-status-item-instance">{state}</div>',
                '<div class="x-clear icinga-overall-status-spacer"></div>',
                '</div>',
                '</tpl>',
                '</div>'
            )
        });
        
        this.instanceView.on('click', function(dview, index, node, e) {
            var d = dview.getStore().getAt(index).data;
            
            if (d.count <= 0) {
                return false;
            }
            
            if (Ext.isEmpty(this.instanceTip)) {
                this.instanceTip = new Ext.ToolTip({
                    autoDestroy : false,
                    title : _('Instance status'),
                    tpl: new Ext.XTemplate(
                        '<tpl for=".">',
                        '<div>{msg}</div>',
                        '</tpl>'
                    ),
                    hideDelay:2000,
                    renderTo : Ext.getBody()
                });
                
                this.instanceTip.render();
            }
            
            this.instanceTip.update(d.msg);
            this.instanceTip.setTitle(_('Instance status'));
            this.instanceTip.showAt(e.getXY());
            
        }, this);
        
        this.add(this.instanceView);
        
		this.doLayout();
	},
    
        loadInstanceDataStore : function(store, targetStore) {
        var data;
        
        try {
            data = store.reader.jsonData.rowsInstanceStatus;
        } catch(e) {
            AppKit.log("FAIL");
            return "";
        }
        
        var out = [[0, 0, []], [2, 0, []]];
        
        Ext.iterate(data, function(item, index) {
            var msg = String.format(
                _('Instance \'{0}\' (status is {1}, data is {2} minutes old)'),
                item.instance,
                item.status,
                Ext.util.Format.round(item.diff / 60, 2)
            );
            
            if (item.status == false) {
                out[1][1] += 1;
                out[1][2].push({msg : msg});
            } else {
                out[0][1] += 1;
                out[0][2].push({msg : msg});
            }
        }, this);
        
        targetStore.loadData(out);
    }
});