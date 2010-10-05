/* 
 * InfoPanel for icinga bp-cronk
 *
 */

Ext.onReady(function() {
	Ext.ns('Cronk.bp');

	var eventGrid = Ext.extend(Ext.grid.GridPanel,{
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				width: 120,
				sortable: true
			},
			columns: [
				{id: 'HOST_NAME', header: _('Host'),dataIndex:'HOST_NAME'},
				{id: 'SERVICE_NAME', header: _('Service'),dataIndex:'SERVICE_NAME'},
				{	
					id: 'STATUS',
					header: _('Status'),
					dataIndex:'STATEHISTORY_STATE',
					renderer: function(value, metaData, record, rowIndex, colIndex, store) {
						var state =''
						switch(value) {
							case '0':
								state = 'OK';
								break;
							case '1':
								state = 'WARNING';
								break;
							case '2':
								state = 'CRITICAL';
								break;
							default:
								state = 'UNKNOWN';
								break;
						}
						return '<div class="icinga-status icinga-status-'+state.toLowerCase()+'" style="height:12px;text-align:center">'+state+'</div>';
					}
				},
				{id: 'LAST_CHECK', header: _('Timestamp'),dataIndex:'STATEHISTORY_STATE_TIME',groupable:false},
				{id: 'ATTEMPT',width:40, header: _('Attempt'),dataIndex:'STATEHISTORY_CURRENT_CHECK_ATTEMPT'},
				{id: 'OUTPUT', header:_('Output'),dataIndex: 'STATEHISTORY_OUTPUT'}
			]			
		}),
		frame:true,
		view: new Ext.grid.GroupingView({
			forceFit:true,
			groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
		})
	})

	Cronk.bp.infoPanel =  Ext.extend(Ext.TabPanel,{
		eventGrid : null,
		autoScroll: true,
		constructor: function(cfg) {
			cfg = cfg || {}
			cfg.activeTab = 0;
			if(!Ext.isArray(cfg.tabItems))
				cfg.tabItems = [cfg.tabItems];
			this.gridStore = new Ext.data.GroupingStore({
				url: cfg.icingaApiURL,
				reader: new Ext.data.JsonReader({
					root: 'result',
					idIndex:0,
					fields: [
						'SERVICE_NAME','HOST_NAME','INSTANCE_NAME','SERVICE_OBJECT_ID',
						'HOST_OBJECT_ID','STATEHISTORY_STATE_TIME','STATEHISTORY_OUTPUT',
						'STATEHISTORY_STATE','STATEHISTORY_CURRENT_CHECK_ATTEMPT',
						'STATEHISTORY_MAX_CHECK_ATTEMPTS'
					]
				}),
				groupField: 'SERVICE_NAME',
				sortInfo: {field: 'STATEHISTORY_STATE_TIME', direction: 'DESC'},
				baseParams: {
					target: 'service_status_history',
					'columns[0]' : 'SERVICE_NAME',
					'columns[1]' : 'HOST_NAME',
					'columns[2]' : 'INSTANCE_NAME',
					'columns[3]' : 'SERVICE_OBJECT_ID',
					'columns[4]' : 'HOST_OBJECT_ID',
					'columns[5]' : 'STATEHISTORY_STATE_TIME',
					'columns[6]' : 'STATEHISTORY_OUTPUT',
					'columns[6]' : 'STATEHISTORY_STATE',
					'columns[7]' : 'STATEHISTORY_CURRENT_CHECK_ATTEMPT',
					'columns[8]' : 'STATEHISTORY_MAX_CHECK_ATTEMPTS',
					'columns[9]' : 'STATEHISTORY_ID',
					'columns[9]' : 'STATEHISTORY_OUTPUT',
			
					'withMeta'				: true,
					'countColumn'			: 'STATEHISTORY_ID',
					'limit_start'			: 0,
					'limit'					: 75
				},
				groupOnSort:true,
				paramNames: {
					limit: 'limit',
					start: 'limit_start',
					sort: 'order_col',
					dir: 'order_dir'
				},
				remoteSort:true,
				remoteGroup:false
			
			});

			this.showFilterPanel = function() {},

			this.eventGrid = new eventGrid({
				autoScroll:true,
				title: _('Events for (no object selected'),
				store:this.gridStore,
				tbar: new Ext.PagingToolbar({
					store: this.gridStore,       // grid and PagingToolbar using same store
					displayInfo: true,
					pageSize: 75,
					prependButtons: true
				})
			});

			cfg.items = [
				this.eventGrid
			];
			/**
			 * @TODO: Perhaps direct cronk integration?
			 */
			
			Ext.each(cfg.tabItems,function(item) {
				cfg.items.push(this.processTabItemFromConfig(item,this));
			},this)

			Ext.apply(this,cfg);
			Ext.TabPanel.prototype.constructor.call(this,cfg);
		},
		events: {
			'displayNode' : true
		},

		listeners: {
			render: function(cmp) {
				cmp.on("displayNode",function(node) {
					this.collectInformation(node);
				},cmp);
			},
			scope:this
		},

		collectInformation: function(node) {
			this.currentNode = node;
			var name = node.attributes.display_name;
			if(name.length >30)
				name = name.substr(0,27)+"...";
			this.eventGrid.setTitle(_('Events for ')+name);
			var services = this.getAllServicesFor(node.attributes);
			this.displayData(services);
		},


		displayData: function(services) {
			var filter = {
				type:'AND',
				field: [{
					type:'OR',
					field: []
				}]
			}
			Ext.each(services,function(service) {
				filter.field[0].field.push({
					type:'AND',
					field: [{
						type: 'atom',
						field: ['SERVICE_NAME'],
						method:['='],
						value: [service.service_name]
					},{
						type: 'atom',
						field: ['HOST_NAME'],
						method:['='],
						value: [service.host_name]
					},{
						type: 'atom',
						field: ['STATEHISTORY_STATE'],
						method:['!='],
						value: ['0']
					}]
				})
			})
			this.gridStore.setBaseParam("filters_json",Ext.encode(filter));
			this.gridStore.load();

		},

		createNewRequestInstance: function() {
			this.requestInformation.semaphore = 0;
			this.requestInformation.requestInstance++;
			this.requestInformation.requestData = [];
		},

		getAllServicesFor: function(node) {
			var services = [];
			if(!node.service_name) {
				Ext.each(node.children,function(childNode) {
					services = services.concat(this.getAllServicesFor(childNode));
				},this);
			} else {
				services = [{
					host_name: node.host_name,
					service_name: node.service_name
				}]
			}
			return services;
		},

		processTabItemFromConfig: function (item,cmp) {
			var panel = new Ext.Panel({
				title: item.title,
				layout:'fit',
				items: {
					html: "<div class='bp_link_infoBox'>Select a matching item first!</div>"
				}
			});
			cmp.on("displayNode",function(node) {
				url = this.processURL(item.url,node, function(url) {
					panel.removeAll();
					panel.add({
						xtype:'panel',
						bodyCfg: {
							tag: 'iframe',
							src: url
						}
					})
					panel.doLayout();
				}, function() {
					panel.removeAll();
					panel.add({
						html: "<div class='bp_link_infoBox'>Select a matching item first!</div>"
					});
					panel.doLayout();
				})
			},this);
			return panel;
		},

		processURL: function(url,node,cb,fail) {
			var isCV =  url.match(/^\$\{CV:(.*)\}$/);
			if(Ext.isArray(isCV)) {
				if(isCV[1] && node.attributes.service_name) {
					this.getCustomVarAsURL(node,isCV[1],cb,fail);
					return true;
				}
			}
			for(var attr in node.attributes) {
				if(!Ext.isString(attr))
					continue;
				url = url.replace("${"+attr+"}",node.attributes[attr]);
			}
			var hasCustomVarReq = /\$\{.*?}/
			var matches = url.match(hasCustomVarReq);
			if(!matches)
				cb(url);
			else
				fail();
		},

		getCustomVarAsURL: function(node,cv,cb,fail) {

			Ext.Ajax.request({
				url: this.icingaApiURL,
				params: {
					'columns[0]':'SERVICE_CUSTOMVARIABLE_VALUE',
					'filters[0][column]' : 'SERVICE_CUSTOMVARIABLE_NAME',
					'filters[0][relation]' : '=',
					'filters[0][value]' : cv,
					'filters[1][column]' : 'SERVICE_NAME',
					'filters[1][relation]' : '=',
					'filters[1][value]' : node.attributes.service_name,
					'filters[2][column]' : 'HOST_NAME',
					'filters[2][relation]' : '=',
					'filters[2][value]' : node.attributes.host_name,
					'target' : 'service'
				},
				success: function(result) {
					result = Ext.decode(result.responseText);
					if(Ext.isObject(result[0])) {
						if(result[0]["SERVICE_CUSTOMVARIABLE_VALUE"]) {
							cb(result[0]["SERVICE_CUSTOMVARIABLE_VALUE"])
							return true;
						}
					}
					fail();
				}, failure: function() {
					fail();
				}
			})

		}
	});
});

