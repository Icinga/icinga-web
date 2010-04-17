<?php 
	$parentid = $rd->getParameter('parentid');
?>
<script type="text/javascript">

(function() {

	var oid = '<?php echo $parentid; ?>';
	var coParent = Ext.getCmp(oid);
	
	var oSearchHandler =  function() {
		
		var val;
		var ctWindow;
		var proxy;
		
		var oStores = {};
		var oViews = {};
		
		var keytime;
		
		var noresult = false;
		
		var titles = {
			'host': 		'Hosts ({0})',
			'service':		'Services ({0})',
			'hostgroup':	'Hostgroups ({0})',
			'servicegroup':	'Servicegroups ({0})'
		};
		
		var stores = ['host', 'service', 'hostgroup', 'servicegroup'];
		
		function oProxy() {
			if (!proxy) {
				proxy = new Ext.data.HttpProxy({
					url: "<?php echo $ro->gen('icinga.cronks.objectsearch.json')?>"
				});
			}
			return proxy;
		}
		
		function oStore(type) {
			if (!oStores[type]) {
				var record = new Ext.data.Record.create([
					{name: 'object_id'},
	    			{name: 'object_name'},
	    			{name: 'description'},
	    			{name: 'object_name2'},
	    			{name: 'object_status'},
	    			
	    			{name: 'data1'},
	    			{name: 'data2'},
	    			{name: 'data3'}
				]);
				
				var reader = new Ext.data.JsonReader({      
				    root: type + '.resultRows',             
				    totalProperty: type + '.resultCount',
				    idProperty: 'object_id' 
				}, record);
				
				oStores[type] = new Ext.data.Store({
					autoLoad: false,
					proxy: oProxy(),
					reader: reader,
					baseParams: {
						t: type
					}
				});
				
				// Write the sums to the title
				oStores[type].on('load', function(store, record, o) {
					Ext.getCmp('osearch-tab-' + type).setTitle(String.format(titles[type], store.getTotalCount()));
					oSearchHandler.calcResultApproach();
					oSearchHandler.checkNoResult();
				})
			}
			
			return oStores[type];
		}
		
		function oTemplate(type) {
			
			var template;
			
			switch (type) {
				
				case 'host':
					template = new Ext.XTemplate(
					    '<tpl for=".">',
					        '<div class="icinga-osearch-wrap" id="{object_name}">',
					        '<div class="thumb"><img ext:qtip="{description}" src="<?php echo AppKitHtmlHelper::Obj()->imageUrl('icinga.idot-small'); ?>"></div>',
					        '<div><span>{object_short_name}</span><br /><span>({data1})</span><br />{object_status}</div>',
					        '</div>',
					    '</tpl>',
					    '<div class="x-clear"></div>'
					);
				break;
				
				case 'service':
					template = new Ext.XTemplate(
					    '<tpl for=".">',
					        '<div class="icinga-osearch-wrap" id="{object_name}" ext:qtip="{description}">',
					        '<div class="thumb"><img ext:qtip="{description}" src="<?php echo AppKitHtmlHelper::Obj()->imageUrl('icinga.idot-small'); ?>"></div>',
					        '<div><span>{object_short_name}</span><br /><span>({object_name2})</span><br />{object_status}</div>', 
					        '</div>',
					    '</tpl>',
					    '<div class="x-clear"></div>'
					);
				break;
				
				default:
					template = new Ext.XTemplate(
					    '<tpl for=".">',
					        '<div class="icinga-osearch-wrap" id="{object_name}">',
					        '<div class="thumb"><img ext:qtip="{description}" src="<?php echo AppKitHtmlHelper::Obj()->imageUrl('icinga.idot-small'); ?>"></div>',
					        '<div><span>{object_short_name}</span></div></div>',
					    '</tpl>',
					    '<div class="x-clear"></div>'
					);
				break;
			}
			
			return template;
		}
		
		function oList(type) {
			
			if (!oViews[type]) {

				var store = oStore(type);
				// store.load({params: {q: 'f'}});
				
				var tpl = oTemplate(type);
				
				oViews[type] = new Ext.DataView({
					store: store,
					reserveScrollOffset: true,
					
					columns: [
						{ header: 'Name', dataIndex: 'object_name' },
						{ header: 'OID', dataIndex: 'object_id' },
						{ header: 'Description', dataIndex: 'description' },
						{ header: 'Image', dataIndex: 'image' }
					],
					
					cls: 'icinga-osearch-frame',
					itemSelector: 'div.icinga-osearch-wrap',
					overClass:'x-view-over',
					emptyText: 'no data',
					trackOver: true,
					singleSelect: true,
					
					prepareData: function(data) {
						data.object_short_name = Ext.util.Format.ellipsis(data.object_name, 10);
						
						if (type == 'host') {
							data.description = String.format('{0}, {1}', data.description, data.data1);
						}
						
						if (data.object_status) {
							data.object_status = Icinga.StatusData.wrapText(type, data.object_status);
						}
						
						return data;
					},
					
					listeners: {
						dblclick: oSearchHandler.doubleClickProc
					},
					
					tpl: tpl
				});
				
				// Setting the type
				oViews[type].object_type = type;
			
			}
			
			return oViews[type];
		}
		
		
		
		function oWindow() {
			if (!ctWindow) {
				ctWindow = new Ext.Window({
					title: 'Search',
					width: 500,
					height: 400,
					closable: false,
					resizable: false,
					layout: 'fit',
					
					buttons: [{
						text: 'Close',
						handler: function(w) {
							oTextField.setValue('');
							oWindow().hide();
						}
					}],
					
					listeners: {
						show: function(w) {
							oTextField.focus(false, 200);
						}
					},
					
					items: [{
						xtype: 'grouptabpanel',
						activeGroup: 0,
						tabWidth: 130,
						
						items: [{
							expanded: true,
							
							defaults: {
								style: 'padding: 10px;',
								autoScroll: true
							},
							
							id: 'osearch-result-tabs',
							
							items: [{
								title: 'Objects'
							}, {
								title: 'Hosts (0)',
								items: oList('host'),
								id: 'osearch-tab-host'
							}, {
								title: 'Services (0)',
								items: oList('service'),
								id: 'osearch-tab-service'
							},{
								title: 'Hostgroups (0)',
								items: oList('hostgroup'),
								id: 'osearch-tab-hostgroup'
							},{
								title: 'Servicegroups (0)',
								items: oList('servicegroup'),
								id: 'osearch-tab-servicegroup'
							}]
						}]
					}]
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
				if (val && val.length >= 1) {
					
					if (!oWindow().isVisible()) {
						var xy = field.getPosition();
						xy[0] += field.getSize().width + 55;
						
						oWindow().setPagePosition(xy);
						oWindow().show(field);
					}
					
					oWindow().setTitle('Search: ' + val);
					
					// Buffer the ajax load
					keytime = new Date();
					
					oSearchHandler.reloadAllStores.defer(90);
					
				}
				else {
					oWindow().hide();
				}
			},
			
			reloadAllStores : function() {
				var testdate = new Date();
				if (keytime && (testdate.getTime() - keytime.getTime()) > 50) {
					
					Ext.each(stores, function(key, index, ary) {
						oStore(key).reload({ params: { q: val } });	
					})
				}
			},
			
			calcResultApproach : function() {
				var mStore = new Array(null,0);
				
				Ext.each(stores, function(key, index, ary) {
						if (oStore(key).getTotalCount() > mStore[1]) {
							mStore[0] = key;
							mStore[1] = oStore(key).getTotalCount();
						}	
				});
				
				if (mStore[0]) {
					Ext.getCmp('osearch-result-tabs').setActiveTab( 'osearch-tab-' + mStore[0] );
				}
			},
			
			checkNoResult : function() {
				var test = 0;
				
				Ext.each(stores, function(key, index, ary) {
					test += oStore(key).getTotalCount();
				});
				
				if (test > 0 && noresult == true) {
					noresult = false;
				}
				
				if (noresult == false && test == 0) {
					noresult = true;
					AppKit.Ext.notifyMessage('Search', 'No results!');
				}
			},
			
			doubleClickProc : function(view, index, node, e) {
				var re = view.getStore().getAt(index);
				var type = view.object_type;
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
				
				AppKit.Ext.util.InterGridUtil.gridFilterLink(cronk, filter);
				
				oWindow().hide();
				oTextField.setValue('');
				
				return true;
			}

		};
		
	}();

	var oTextField = new Ext.ux.form.FancyTextField({
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
	
	oSearchHandler.oTextField = oTextField;
	
	var oSearch = new Ext.FormPanel({
		frame: false,
		labelWidth: 0,
		border: false,
		defaultType: 'textfield',
		items: [oTextField]
	});
	
	coParent.add(oSearch);
	
	coParent.add(oSearch);
	
})();

</script>
