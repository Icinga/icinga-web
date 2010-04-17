<?php 
	$parentid	= $rd->getParameter('parentid');
	$stateuid	= $rd->getParameter('stateuid');	
	$columns	= $rd->getParameter('columns');  
	
	$width		= floor(100 / $columns) / 100;
?>
<script type="text/javascript">

	(function() {
				
		var PortalHandler = function() {
			
			var pub = {};
			
			Ext.apply(pub, {
				
				createResizer : function (portlet) {
					// Adding an resizer
					portlet.on('render', function(ct,position) {
						
						var createProxyProtoType=Ext.Element.prototype.createProxy;
				        Ext.Element.prototype.createProxy=function(config){
					        return Ext.DomHelper.append(ct.getEl(), config, true);
					    };
						
						this.resizer = new Ext.Resizable(this.el, {
				            animate: true,
				            duration: this.duration,
				            easing: this.easing,
				            handles: 's',
				            transparent:this.transparent,
				            heightIncrement:this.heightIncrement,
				            minHeight: this.minHeight || 100,
				            pinned: this.pinned
				        });
				        
				        this.resizer.on('resize', function(oResizable, iWidth, iHeight, e) {
				        	this.setHeight(iHeight);				        	
				        }, this);
				        
				        Ext.Element.prototype.createProxy=createProxyProtoType;
				        
					});
				},
				
				createPortletDragZone : function (p) {
						var cdz = new Ext.dd.DropTarget(p.getEl(), {
							ddGroup: 'cronk',
							grid: undefined,
							ac: undefined,
							
							notifyOut : function(){
						        delete this.grid;
						        delete this.ac;
						    },
							
							notifyOver: function(dd, e, data) {
								
								if (data.dragData.id.indexOf('portalView') == 0) {
//									return false;
									return this.dropNotAllowed;
								}
								
								if (!this.grid) {
									this.grid = p.dd.getGrid();
								}
								
								var xy = e.getXY();
								
								Ext.iterate(this.grid.columnX, function (item, index, arry) {
									
									if (xy[0] >= item.x && xy[0] < item.x+item.w ) {
										this.ac = index;
//										return false;
										return this.dropNotAllowed;
									}
									
								}, this);
								// return this.superclass().notifyOver.call(this, dd, e, data);
								
								return Ext.dd.DropTarget.prototype.notifyOver.call(this, dd, e, data);
								
								// return this.dropAllowed;
							},
							
							notifyDrop: function(dd, e, data) {
								
								var id = Ext.id(null, 'cronk');
								
								var params = {
									'p[parentid]': id
								};
							
								if (data.dragData.parameter) {
									for (var k in data.dragData.parameter) {
										params['p[' + k + ']'] = data.dragData.parameter[k];
									}
								}
								
								var portlet  = AppKit.Ext.CronkMgr.create({
									parentid: id,
									id: id,
									
									params: data.dragData.parameter,
									loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
									crname: data.dragData.id,
									
									title: data.dragData.name,
									closable: true,
									xtype: 'portlet',
									tools: tools,
									height: 200,
									border: true,
									
									// Resizer properties
									heightIncrement:16,
								    pinned:true,
								    duration: .6,
								    transparent:false
								});
								
								PortalHandler.createResizer(portlet);
								
								// Add them to the portal
								p.items.get(this.ac || 0).add(portlet);
								
								// Bubbling render event
								portlet.show();	// Needed for webkit
								portal.doLayout();
								
								// Redefine the updater to held default properties
								/* portlet.getUpdater().setDefaultUrl({
									url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>" + data.dragData.id,
									params: params,
									scripts: true
								});
								
								// initial refresh
								portlet.getUpdater().refresh(); */
							}
						});
				},
				
				itemModifier: function (co, item, index) {
					// Enable events handled by owner
					// persistence
					item.enableBubble(['titlechange', 'resize']);
					
					return true;
				}
			});
			
			return pub;
			
		}();
		
		var p_columns = "<?php echo $columns; ?>";
		var p_width   = "<?php echo $width; ?>";
		
		// Toolbar of the portlet panels
		var tools = [{
			id: 'edit', // x-tools-edit (with a slik icon in silk-icons.css)
			handler: function(e, target, panel) {
				var msg = Ext.Msg.prompt('<?php echo $tm->_("Enter title"); ?>', '<?php echo $tm->_("Change title for this portlet"); ?>', function(btn, text) {
					if (btn == 'ok' && text) {
						panel.setTitle(text);
					}		
				}, this, false, panel.title);
				
				msg.getDialog().alignTo(panel.getEl(), 'tr-tr');
		    }	
		},{
			id:'refresh',
			handler: function(e, target, panel) {
				panel.getUpdater().refresh();
			}
		},{
		    id:'close',
		    handler: function(e, target, panel) {
		        panel.destroy();
		    }
		
		}];
		
		// The configuration for the
		// portal component
		var portal_config = {
		    layout: 'column',
		    autoScroll: true,
		    border: false,
		    
		    listeners: {
		    	render: PortalHandler.createPortletDragZone,
		    	add: PortalHandler.itemModifier
		    }
		};
		
		var items_config = new Array(p_columns);
		
		for (var i=0; i<p_columns; i++) {
			items_config[i] = {
				columnWidth: p_width,
	        	style: 'padding: 3px;'
			};
		}
		
		portal_config.items = items_config;
		
		var cmp = Ext.getCmp("<?php echo $parentid; ?>");
		
		// We need a state id from the cronkmanager, the parent id
		// is a good choice
		var stateuid = "<?php echo $stateuid; ?>";
		if (stateuid) {
			Ext.apply(portal_config, {
				id: id,
				stateId: stateuid,
				stateful: true,
				
				// @todo The collapse event does not work?
				stateEvents: ['add', 'remove', 'titlechange', 'resize'],
				
				getState: function () {
					
					var d = new Array(this.items.getCount());
					
					this.items.each(function (col, cindex, l1) {
						
						d[cindex] = {};
						
						col.items.each(function (cr, crindex, l2) {
							
							if (cr.iscronk && cr.iscronk == true) {
								var c = AppKit.Ext.CronkMgr.getCronk(cr.cronkkey);
								var cronk = AppKit.Ext.CronkMgr.getCronkComponent(cr.cronkkey);
								
								c.config.title = cronk.title;
								c.config.height = cronk.getHeight();
								c.config.collapsed = cronk.collapsed;
//								console.log("COL: " + cronk.collapsed);
								d[cindex][cronk.getId()] = c;
							}
							
						}, this);
						
					}, this);
					
	//				console.log(d);
					
					return {
						col: d,
						title: this.title
					}
				},
				
				applyState: function (state) {
	
					// Defered execution
					(function() {
	
						if (state.col) {
							Ext.each(state.col, function (item, index, arry) {
								Ext.iterate(item, function (key, citem, o) {
									var c = {}
									Ext.apply(c, citem.config, citem.crconf);
//									console.log(c);
									c.tools = tools;
									
									var cronk = AppKit.Ext.CronkMgr.create(c);
									
									PortalHandler.createResizer(cronk);
									
									this.get(index).add(cronk);
									cronk.show();
									
								}, this);
								
							}, this);
							
							this.doLayout();
						}
	
					}).defer(200, this);
					
				}
			});
		}
		
		var portal = new Ext.ux.Portal(portal_config);
		
		cmp.insert(0, portal);
		
		cmp.doLayout();
		
	})();
	
	
</script>
