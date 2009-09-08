<?php 
	$htmlid		= $rd->getParameter('htmlid');
	$columns	= $rd->getParameter('columns');  
	
	$width		= floor(100 / $columns) / 100;
?>
<script type="text/javascript">

	function createPortal() {
		
		var p_columns = "<?php echo $columns; ?>";
		var p_width   = "<?php echo $width; ?>";
		
		var tools = [{
		    id: 'gear',
		    handler: function() {
		        Ext.Msg.alert('Message', 'The Settings tool was clicked.');
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
		
		var portal_config = {
		    layout: 'column',
		    autoScroll: true,
		    listeners: {
		    	render: createPortletDragZone
		    }
		};
		
		function createPortletDragZone(p) {
				var cdz = new Ext.dd.DropTarget(p.getEl(), {
					ddGroup: 'cronk',
					
					notifyDrop: function(dd, e, data){
						
						var id = AppKit.genRandomId('cronk-');
						
						var params = {
							'p[htmlid]': id
						};
					
						if (data.dragData.parameter) {
							for (var k in data.dragData.parameter) {
								params['p[' + k + ']'] = data.dragData.parameter[k];
							}
						}
						
						// Our portlet
						var portlet = new Ext.ux.Portlet({
							title: data.dragData.name,
							closable: true,
							layout: 'fit',
							
							tools: tools,
							id: id,
							
							height: 200,
							
							// Resizer properties
							heightIncrement:16,
						    pinned:true,
						    duration: .6,
						    transparent:false
							
						})
						
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
						p.items.get(0).add(portlet);
						
						// Bubble the render event
						p.doLayout();
						
						// Redefine the updater to held default properties
						portlet.getUpdater().setDefaultUrl({
							url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>" + data.dragData.id,
							params: params,
							scripts: true
						});
						
						// initial refresh
						portlet.getUpdater().refresh();
						
					}
				});
		}
		
		var items_config = new Array(p_columns);
		
		for (var i=0; i<p_columns; i++) {
			items_config[i] = {
				columnWidth: p_width,
	        	style: 'padding: 3px;',
			};
		}
		
		portal_config.items = items_config;
		
		var cmp = Ext.getCmp("<?php echo $htmlid; ?>");
		cmp.insert(0,new Ext.ux.Portal(portal_config));
		
		Ext.getCmp('view-container').doLayout();
	}
	
	createPortal();
	
	
</script>