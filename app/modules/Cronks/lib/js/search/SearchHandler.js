Ext.ns('Icinga.Cronks.search');

Icinga.Cronks.search.SearchHandler = (new (Ext.extend(Ext.util.Observable, {
	
	proxyUrl : null,
	minimumChars : 2,
	
	templates : {
        host:       new Ext.Template('{object_name}({data1})<br /><em>{description}</em>'),
        service:    new Ext.Template('{object_name2}, {object_name}<br /><em>{description}</em>'),
        def:        new Ext.Template('{object_name}<br /><em>{description}</em>')
    },
	
	constructor : function(config) {
		config = config || {}
		this.listeners = config.listeners
		Ext.util.Observable.prototype.constructor.call(config);
	},
	
	register : function() {
		AppKit.search.SearchHandler.on('process', this.handleSearch, this);
		AppKit.search.SearchHandler.on('deactivate', function() {
			this.getWindow().hide();
		}, this);
	},
	
	setProxyUrl : function(url) {
		this.proxyUrl = url;
	},
	
	setMinimumChars : function(chars) {
		this.minimumChars = chars;
	},

	handleSearch : function(handler, query) {
		var wnd = this.getWindow();
		
		if (query.length >= this.minimumChars) {
			if (wnd.isVisible() === false) {
				wnd.show(handler.getTargetElement());
			}
			
			wnd.setTitle(String.format(_('Search for: {0}'), query));
			
			this.filterStore(query);
			
		} else {
			// wnd.hide();
		}
	},
	
    rObjectName : function(value, metaData, record, rowIndex, colIndex, store) {
        var d = record.data;
        var type = d['type'];
        
        var template = this.templates[type] || this.templates['def'];
        return template.apply(d);
    },
    
    rTypeName : function(value, metaData, record, rowIndex, colIndex, store) {
        var cls = Icinga.DEFAULTS.OBJECT_TYPES[record.data.type].iconClass || 'icinga-icon-brick';
        metaData.css = cls;
        return '';
    },
	
	getWindow : function() {
		if (Ext.isEmpty(this.oWindow)) {
            this.oWindow = new Ext.Window({
                title: _('Search'),
                width: 500,
                height: 400,
                closable: false,
                closeMethod : 'hide',
                resizable: false,
                layout: 'fit',
                
                buttons: [{
                    text: _('Close'),
                    iconCls: 'icinga-icon-close',
                    handler: function(button, event) {
                    	this.getWindow().hide();
                    },
                    scope : this
                }],
                
                listeners: {
                    show: function(w) {
                    	var h = AppKit.search.SearchHandler.getSearchbox();
                    	AppKit.search.SearchHandler.activate();
                        h.focus(false, 100);
                    },
                    hide : function() {
                    	AppKit.search.SearchHandler.deactivate();
                    }
                },
                
                items: this.getGrid()
            });
		}
		
		return this.oWindow;
	},
	
	getStore : function() {
		if (Ext.isEmpty(this.oStore)) {
            var record = new Ext.data.Record.create([
                {name: 'type'},
                {name: 'object_id'},
                {name: 'object_name'},
                {name: 'object_name2'},
                {name: 'description'},
                {name: 'object_status'},
                
                {name: 'data1'},
                {name: 'data2'},
                {name: 'data3'}
            ]);
            
            var reader = new Ext.data.JsonReader({      
                root: 'resultRows',             
                totalProperty: 'resultCount',
                idProperty: 'object_id' 
            }, record);
            
            this.oStore = new Ext.data.GroupingStore({
                autoLoad: false,
                proxy: new Ext.data.HttpProxy({
	                url: this.proxyUrl
	            }),
                reader: reader,
                remoteGroup: true,
                remoteSort: true,
                groupField: 'type'
            });
            
            this.oStore.on('load', this.calcResultApproach, this);
		}
		
		return this.oStore;
	},
	
	filterStore : function(val) {
		this.getStore().reload({ params: { q: val } });
	},
	
	calcResultApproach : function() {
        this.getGrid().getView().collapseAllGroups();
        this.getGrid().getView().toggleRowIndex(0, true);
	},
	
	getGrid : function() {
		if (Ext.isEmpty(this.oGrid)) {
            var colModel = new Ext.grid.ColumnModel({
                columns: [
                    { header: _('id'),
                      id: 'object_id',
                      hidden:true,
                      dataIndex: 'object_id' },
                      
                    { header: _('Type'),
                      hidden: false,
                      width: 12,
                      dataIndex: 'type',
                      renderer: { fn: this.rTypeName, scope: this } ,
                      groupRenderer: String },
                      
                    { header: _('Name'),
                      dataIndex: 'object_name',
                      renderer: { fn: this.rObjectName, scope: this } },
                      
                    { header: _('Description'),
                      width: 80,
                      hidden:true,
                      dataIndex: 'description' },
                      
                    { header: _('Status'),
                      width: 30,
                      dataIndex: 'object_status',
                      renderer: { fn: Icinga.StatusData.renderSwitch, scope: this } }
                      
                ],
                
                defaults: {
                    sortable: false,
                    menuDisabled: true,
                    groupable: false
                }
                
            });
            
            this.oGrid = new Ext.grid.GridPanel({
                
                layout: 'fit',
                
                colModel: colModel,
                
                view: new Ext.grid.GroupingView({
                    autoFill: true,
                    forceFit:true,
                    startCollapsed: true,
                    groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? _("Items") : _("Item")]})'
                }),
                
                store: this.getStore()
            });
            
            this.oGrid.on('cellclick', this.doubleClickProc, this);
		}
		
		return this.oGrid;
	},
	
    doubleClickProc : function(grid, rowIndex, columnIndex, e) {
        var re = grid.getStore().getAt(rowIndex);
        var type = re.data.type;
        
        var params = {  
            module: 'Cronks',
            action: 'System.ViewProc'
        };
        var filter = {};
        
        
        var id = (type || 'empty') + 'searchResultComponent'+Ext.id();
        
        switch (type) {
            case 'host':
                filter['f[host_object_id-value]'] = re.data.object_id;
                filter['f[host_object_id-operator]'] = 50;
                params['template'] = 'icinga-service-template';
            break;
            
            case 'service':
                filter['f[service_object_id-value]'] = re.data.object_id;
                filter['f[service_object_id-operator]'] = 50;
                params['template'] = 'icinga-service-template';
            break;
            
            case 'hostgroup':
                filter['f[hostgroup_object_id-value]'] = re.data.object_id;
                filter['f[hostgroup_object_id-operator]'] = 50;
                params['template'] = 'icinga-host-template';
            break;
            
            case 'servicegroup':
                filter['f[servicegroup_object_id-value]'] = re.data.object_id;
                filter['f[servicegroup_object_id-operator]'] = 50;
                params['template'] = 'icinga-service-template';
            break;
            
            default:
                Ext.Msg.alert('Search', 'This type is not ready implemented yet!');
                return;
            break;
        }
        
        var cronk = {
            parentid: id,
            title: 'Search result ' + type,
            crname: 'gridProc',
            closable: true,
            allowDuplicate: true,
            params: params
        };
        
        Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
        
        return true;
    }
	
})));