<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	
	var oSearchHandler =  function() {
		
		/**
		 * Start search if minimum x chars entered
		 * @type Integer
		 */	
		var minCharacters = 4;
		
		var val;
		var ctWindow;
		var proxy;
		
		var oTextField = null;
		var oGrid = null;
		var oStoreObj = null;
		var oViews = {};
		
		var titles = {
			'host': 		'Hosts ({0})',
			'service':		'Services ({0})',
			'hostgroup':	'Hostgroups ({0})',
			'servicegroup':	'Servicegroups ({0})'
		};
		
		var templates = {
			host:		new Ext.Template('{object_name}({data1})<br /><em>{description}</em>'),
			service:	new Ext.Template('{object_name2}, {object_name}<br /><em>{description}</em>'),
			def:		new Ext.Template('{object_name}<br /><em>{description}</em>')
		}
		
		var stores = ['host', 'service', 'hostgroup', 'servicegroup'];
		
		function oProxy() {
			if (!proxy) {
				proxy = new Ext.data.HttpProxy({
					url: "<?php echo $ro->gen('cronks.objectsearch.json')?>"
				});
			}
			return proxy;
		}
		
		function oStore() {
			
			if (!oStoreObj) {
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
				
				oStoreObj = new Ext.data.GroupingStore({
					autoLoad: false,
					proxy: oProxy(),
					reader: reader,
					remoteGroup: true,
					remoteSort: true,
					groupField: 'type',
					
					listeners: {
						load: oSearchHandler.calcResultApproach
					}
				});
			}
			
			return oStoreObj;
		}
		
		function rObjectName(value, metaData, record, rowIndex, colIndex, store) {
			var d = record.data;
			var type = d['type'];
			
			var template = templates[type] || templates['def'];
			return template.apply(d);
		}
		
		function rTypeName(value, metaData, record, rowIndex, colIndex, store) {
			var cls = Icinga.DEFAULTS.OBJECT_TYPES[record.data.type].iconClass || 'icinga-icon-brick';
			metaData.css = cls;
			return '';
		}
		
		function oGridResult() {
			if (!oGrid) {
				
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
						  renderer: { fn: rTypeName, scope: this } ,
						  groupRenderer: String },
						  
						{ header: _('Name'),
						  dataIndex: 'object_name',
						  renderer: { fn: rObjectName, scope: this } },
						  
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
				
				oGrid = new Ext.grid.GridPanel({
					
					layout: 'fit',
					
					colModel: colModel,
					
					view: new Ext.grid.GroupingView({
						autoFill: true,
			            forceFit:true,
			            startCollapsed: true,
			            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? _("Items") : _("Item")]})'
			        }),
					
					listeners: {
						cellclick: oSearchHandler.doubleClickProc
					},
					
					store: oStore()
				});
			}
			
			return oGrid;
		}
		
		function oWindow() {
			if (!ctWindow) {
				ctWindow = new Ext.Window({
					title: _('Search'),
					width: 500,
					height: 400,
					closable: false,
					resizable: false,
					layout: 'fit',
					
					buttons: [{
						text: _('Close'),
						handler: function(w) {
							oTextField.setValue('');
							oWindow().hide();
						}
					}],
					
					listeners: {
						show: function(w) {
							oTextField.focus(false, 10);
						}
					},
					
					items: oGridResult()
				});
			}
			
			return ctWindow;
		}
		
		return {
			
			/*
			 * Keyup handler
			 */
			keyup : function(field, e) {
				val = field.getValue();
				
				// 27 == ESC
				if (e.getCharCode() == 27) {
					field.setValue('');
					oWindow().hide();
				}
				else if (!Ext.isEmpty(val) && val.length >= minCharacters) {
					if (!oWindow().isVisible()) {
						var xy = field.getPosition();
						xy[0] += field.getSize().width + 55;
						
						oWindow().setPagePosition(xy);
						oWindow().show(field);
					}
					
					oWindow().setTitle('Search: ' + val);
					
					oSearchHandler.reloadAllStores.defer(20, this, [val])
					
				}
				else {
					oWindow().hide();
				}
			},
			
			reloadAllStores : function(val) {
				oStore().reload({ params: { q: val } });
			},
			
			calcResultApproach : function() {
				oGrid.getView().collapseAllGroups();
				oGrid.getView().toggleRowIndex(0, true);
			},
			
			// celldblclick: 
			doubleClickProc : function(grid, rowIndex, columnIndex, e) {
				var re = grid.getStore().getAt(rowIndex);
				var type = re.data.type;
				
				var params = {};
				var filter = {};
				
				
				var id = (type || 'empty') + 'searchResultComponent';
				
				switch (type) {
					case 'host':
						filter['f[host_object_id-value]'] = re.data.object_id;
						filter['f[host_object_id-operator]'] = 50;
						params['template'] = 'icinga-host-template';
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
					params: params
				};
				
				Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
				
				oWindow().hide();
				oTextField.setValue('');
				
				return true;
			},
			
			setTextField : function(f) {
				oTextField = f;
			}

		};
		
	}();

	var myTextField = new Ext.ux.form.FancyTextField({
		title: 'Search',
		xtype: 'fancytextfield',
		name: 'q',
		enableKeyEvents: true,
		resetButton: false,
		width: 180,
		listeners: {
			keyup: {
				fn: oSearchHandler.keyup,
				delay: 100
			}
		}
	});
	
	oSearchHandler.setTextField(myTextField);
	
	var oSearch = new Ext.FormPanel({
		frame: false,
		labelWidth: 0,
		border: false,
		defaultType: 'textfield',
		items: myTextField
	});
	
	
	this.add(oSearch);
	this.doLayout();
});
//-->
</script>
