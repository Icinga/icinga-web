Ext.onReady(function() {
	Ext.ns('Cronk.bp');

	Cronk.bp.eventGrid = Ext.extend(Ext.grid.GridPanel,{
		constructor: function(cfg) {
			Ext.apply(this,cfg);
			
			Ext.grid.GridPanel.superclass.constructor.call(this,cfg);
		
		},

		createFunkyInputLayer: function(x,y,w,h,cfg) {
			var funkyInputLayer = new Ext.Layer({
				shadow:!AppKit.util.fastMode(),
				constrain:true
			});
			funkyInputLayer.setBounds(x,y+10,w, h);
			funkyInputLayer.setStyle({
				'-moz-border-radius': '5px',
				'-webkit-border-radius': '5px',
				'border': '1px solid #cecece',
				'background-color':'#dedede',
				'padding' : '5px',
				'overflow' : 'hidden'
			});
			
			funkyInputLayer.container = new Ext.Container(Ext.apply({
				renderTo:funkyInputLayer,
				layout:'form'
			},cfg));

			return funkyInputLayer;
		},

		
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
		disableSelection:true,
		frame:true,
		view: new (Ext.extend(Ext.grid.GroupingView,{
				onRowOver: function(e,t) {
					// show row info on mouseover
					try {
						var grid = this.grid;
						var idx = this.findRowIndex(t);
							
						Ext.grid.GroupingView.prototype.onRowOver.call(this,e,t);
						// test if we need a new layer or if we can just move this layer
						if(grid.currentInfo) {
						/*	if(grid.currentInfo[1] == idx) {
								grid.currentInfo[0].setPageX(e.getPageX());
								grid.currentInfo[0].setPageY(e.getPageY()+10);
								return true;
							}*/
						//	grid.currentInfo[0].hide();
						}	
						 
						grid.showInfo(grid,idx,e);
					} catch(e) {}
				},
				onRowOut: function(e,t) {
					var grid = this.grid;
					if(grid.currentInfo) {
						grid.currentInfo[0].hide();
						grid.currentInfo = null;
					}
					Ext.grid.GroupingView.prototype.onRowOut.call(this,e,t);
				}
			}))({
				forceFit:true,
				groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
			}),

		updateContent: function(node) {
				this.currentNode = node;
				var name = node.attributes.display_name;
				if(name.length >30)
					name = name.substr(0,27)+"...";
				this.setTitle(_('History for ')+name);
				var services = this.getAllServicesFor(node.attributes);
				this.loadContentForServices(services);
		},



		loadContentForServices: function(services) {
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
							field: ['STATEHISTORY_OUTPUT'],
							method:['not like'],
							value: ['Force Service OK%']
						}]
					})
				})
				this.getStore().setBaseParam("filters_json",Ext.encode(filter));
				this.getStore().load();

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
		}
	})
})
