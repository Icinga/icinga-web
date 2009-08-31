<?php 
	$htmlid		= $rd->getParameter('htmlid');
	$columns	= $rd->getParameter('columns');  
	
	$width		= floor(100 / $columns) / 100;
?>
<script type="text/javascript">

	function createPortal() {
		
		var p_columns = <?php echo $columns; ?>;
		var p_width   = <?php echo $width; ?>;
		
		var tools = [{

		    id:'gear',
		
		    handler: function(){
		        Ext.Msg.alert('Message', 'The Settings tool was clicked.');
		    }
		
		},{
		
		    id:'close',
		    handler: function(e, target, panel){
		        panel.ownerCt.remove(panel, true);
		    }
		
		}];
		
		var portal_config = {
		    layout: 'column',
		    
		    height: Ext.getCmp("<?php echo $htmlid; ?>").getHeight(),
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
						
						p.items.get(0).add({
							title: data.dragData.name,
							closable: true,
							canResize: true,
						    heightIncrement:16,
						    pinned:false,
						    duration: .6,
						    easing: 'backIn',
						    transparent:false,
							
							tools: tools,
							id: id,
							
							autoLoad: { 
								url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>" + data.dragData.id,
								scripts: true,
								params: params
							},
							
							listeners: {
								/* render: function(ct, position) {
								    
								    var createProxyProtoType=Ext.Element.prototype.createProxy;
								    
							        Ext.Element.prototype.createProxy=function(config){
								        return Ext.DomHelper.append(this.dom, config, true);
								    };
								    
								    
								    if(this.canResize==true){
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
							        	this.resizer.on('resize', this.onResizer, this);
									}
									
									Ext.Element.prototype.createProxy=createProxyProtoType;
									
								},
								
								onResizer : function(oResizable, iWidth, iHeight, e) {
        							this.setHeight(iHeight);
    							},
    							
    							onCollapse : function(doAnim, animArg) {
							        this.el.setHeight('');  
							        Ext.ux.Portlet.superclass.onCollapse.call(this, doAnim, animArg);
							    } */
    								
							}
						});
						
						
						
						p.doLayout();
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
		
		var cmp = Ext.getCmp("<?php echo $htmlid; ?>");
		cmp.add(new Ext.ux.Portal(portal_config));
		
		Ext.getCmp('cronk-tabs').doLayout();
		Ext.getCmp('cronk-container').doLayout();
	}
	
	createPortal();
	
	
</script>