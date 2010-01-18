<?php 
	$parentid		= $rd->getParameter('parentid');
	$columns	= $rd->getParameter('columns');  
	
	$width		= floor(100 / $columns) / 100;
?>
<script type="text/javascript">

	(function() {
		
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
		        panel.ownerCt.remove(panel, true);
		    }
		
		}];
		
		// The configuration for the
		// portal component
		var portal_config = {
		    layout: 'column',
		    autoScroll: true,
		    border: false,
		    
		    listeners: {
		    	render: createPortletDragZone
		    }
		};
		
		function createPortletDragZone(p) {
				var cdz = new Ext.dd.DropTarget(p.getEl(), {
					ddGroup: 'cronk',
					grid: undefined,
					ac: undefined,
					
					notifyOut : function(){
				        delete this.grid;
				        delete this.ac;
				    },
					
					notifyOver: function(dd, e, data) {
						
//						console.log("--- START ---");
						
						if (!this.grid) {
							this.grid = p.dd.getGrid();
						}
						
						var xy = e.getXY();
						
//						console.log(xy);
//						console.log(this.grid.columnX);
						
						Ext.iterate(this.grid.columnX, function (item, index, arry) {
							
//						console.log(item);
// 	
							if (xy[0] >= item.x && xy[0] < item.x+item.w ) {
								this.ac = index;
								return false;
							}
							
					}, this);
						
//						console.log(this.ac);
//						
//						console.log("--- STOP ---");

						return Ext.dd.DropTarget.prototype.notifyOver.call(this, dd, e, data);
					},
					
					notifyDrop: function(dd, e, data) {
						
						var id = AppKit.Ext.genRandomId('cronk-');
						
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
							layout: 'fit',
							xtype: 'portlet',
							
							tools: tools,
							
							height: 200,
							
							// Resizer properties
							heightIncrement:16,
						    pinned:true,
						    duration: .6,
						    transparent:false
						});
						
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
						
						// Add them to the portal
						p.items.get(this.ac || 0).add(portlet);
						
						this.ac = undefined;
						
						// Bubble the render event
						p.doLayout();
						
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
		}
		
		var items_config = new Array(p_columns);
		
		for (var i=0; i<p_columns; i++) {
			items_config[i] = {
				columnWidth: p_width,
	        	style: 'padding: 3px;'
			};
		}
		
		portal_config.items = items_config;
		
		var cmp = Ext.getCmp("<?php echo $parentid; ?>");
		cmp.insert(0,new Ext.ux.Portal(portal_config));
		
		Ext.getCmp('view-container').doLayout();
		
	})();
	
	
</script>
