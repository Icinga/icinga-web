<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<script type="text/javascript">
	var oid = '<?php echo $htmlid; ?>';
	var coParent = Ext.getCmp(oid);
	
	var oSearchHandler =  function() {
		var val;
		var ctWindow;
		var proxy;
		
		var oStores = {};
		var oViews = {};
		
		var titles = {
			'host': 		'Hosts ({0})',
			'service':		'Services ({0})',
			'hostgroup':	'Hostgroups ({0})',
			'servicegroup':	'Servicegroups ({0})'
		};
		
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
	    			{name: 'object_name'},
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
				})
			}
			
			return oStores[type];
		}
		
		function oList(type) {
			
			if (!oViews[type]) {

				var store = oStore(type);
				// store.load({params: {q: 'f'}});
				
				var tpl = new Ext.XTemplate(
				    '<tpl for=".">',
				        '<div class="icinga-osearch-wrap" id="{object_name}" ext:qtip="{description}">',
				        '<div class="thumb"><img src="<?php echo AppKitHtmlHelper::Obj()->imageUrl('icinga.idot-small'); ?>" title="{object_name}"></div>',
				        '<span>{object_name}</span></div>',
				    '</tpl>',
				    '<div class="x-clear"></div>'
				);
				
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
						data.object_name = Ext.util.Format.ellipsis(data.object_name, 15);
						return data;
					},
					
					tpl: tpl
				});
			
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
							defaults: {
								style: 'padding: 10px;'
							},
							
							items: [{
								title: 'Absolut',
								html: AppKit.Ext.bogusMarkup
							}]
						}, {
							expanded: true,
							
							defaults: {
								style: 'padding: 10px;',
								autoScroll: true
							},
							
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
						xy[0] += field.getSize().width + 10;
						
						oWindow().setPagePosition(xy);
						oWindow().show(field);
					}
					
					oWindow().setTitle('Search: ' + val);
					
					// On keyup, reload all available stores
					var stores = ['host', 'service', 'hostgroup', 'servicegroup'];
					for (var i=0;i<stores.length;i++) {
						oStore(stores[i]).reload({ params: { q: val } });
					}
				}
				else {
					oWindow().hide();
				}
			}

		};
		
	}();
	
	var oTextField = new Ext.form.TextField({
		title: 'Search',
		xtype: 'textfield',
		name: 'q',
		enableKeyEvents: true,
		
		listeners: {
			keyup: {
				fn: oSearchHandler.keyup,
				delay: 100
			}
		}
	});
	
	oSearchHandler.oTextField = oTextField;
	
	var oSearch = new Ext.FormPanel({
		labelWidth: 80,
		frame: false,
		labelWidth: 1,
		border: false,
		defaultType: 'textfield',
		
		style: {
			padding: '2px 2px 2px 2px'
		},
		
		items: [oTextField],
	});
	
	coParent.add(oSearch);
	
	Ext.getCmp('north-frame').doLayout();
</script>