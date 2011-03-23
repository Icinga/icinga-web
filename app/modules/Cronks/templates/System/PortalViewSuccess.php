<script type="text/javascript">
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {

		var CE = this;

		var PortalHandler = function() {

			var id = CE.cmpid;
			var pub = {};

			Ext.apply(pub, {

				initPortlet : function(portlet) {
					Cronk.Registry.add(portlet.initialConfig);
					portlet.on('afterlayout',function(ct) {
						
						var params = ct.initialConfig.params;
				;
						params["stateuid"] = ct.stateuid;
						params["p[stateuid]"] = ct.stateuid,
						params["p[parentid]"] = ct.id;
						
						portlet.getUpdater().setDefaultUrl({
							url: "<?php echo $ro->gen('cronks.crloader', array('cronk' => null)); ?>"+ct.crname,
							params: params,
							scripts: true							
						});
						
						portlet.getUpdater().refresh();
					},this,{single:true});

					portlet.on("add",function(el,resp) {
						Ext.each(portlet.findByType('container'),function(item) {
        					item.setHeight(portlet.getInnerHeight());
        				});
					});


					
					/**
					 * Fix width and height
					 * This must be done via one-shot eventdispatcher to avoid
					 * endless recursion (resize->change width->width changed->resize->...)
					 */
					var resizeFunc = function(el) {
						Ext.each(portlet.findByType('container'),function(item) {	
							item.setWidth(portlet.getInnerWidth());
							item.setHeight(portlet.getInnerHeight());
		        		});		
		        		// Attach the listener again after resize
						portlet.on('resize',resizeFunc,this,{single:true})

					}	
					portlet.on('resize',resizeFunc,this,{single:true}); 
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
								
								if (data.dragData.cronkid.indexOf('portalView') == 0) {
									return this.dropNotAllowed;
								}

								if (!this.grid) {
									this.grid = p.dd.getGrid();
								}

								var xy = e.getXY();

								Ext.iterate(this.grid.columnX, function (item, index, arry) {

									if (xy[0] >= item.x && xy[0] < item.x+item.w ) {
										this.ac = index;
										return this.dropNotAllowed;
									}

								}, this);
								// return this.superclass().notifyOver.call(this, dd, e, data);

								return Ext.dd.DropTarget.prototype.notifyOver.call(this, dd, e, data);

								// return this.dropAllowed;
							},

							notifyDrop: function(dd, e, data) {
								var params = {
									module: 'Cronks',
									action: 'System.PortalView',
									'p[parentid]': id
								};
								data.dragData.parameter = data.dragData.parameter || {};
								if (Ext.apply(data.dragData.parameter, data.dragData["ae:parameter"] || {})) {
									for (var k in data.dragData.parameter) {
										params['p[' + k + ']'] = data.dragData.parameter[k];
									}
								}
								

								var portlet  = Cronk.factory({
									id: Ext.id(),

									params: params,
									crname: data.dragData.cronkid,
									stateuid: Ext.id('cronk-sid'),
									title: data.dragData.name,
									closable: true,
									stateful:true,
									xtype: 'portlet',
									tools: tools,
									height: 200,
									border: true
									
								});
								
								PortalHandler.initPortlet(portlet);

								// Add them to the portal
							
								p.items.get(this.ac || 0).add(portlet);
							
								// Bubbling render event

								portlet.show();	// Needed for webkit
								portal.doLayout();
							
								
								// initial refresh
//								portlet.getUpdater().refresh();
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

		var p_columns = CE.getParameter('columns', 1);
		var p_width   = Math.floor(100 / p_columns) / 100;

		// Toolbar of the portlet panels
		var tools = [{
			id: 'gear',
			handler: function(e, target, panel) {
				var msg = Ext.Msg.prompt('<?php echo $tm->_("Enter title"); ?>', '<?php echo $tm->_("Change title for this portlet"); ?>', function(btn, text) {
					if (btn == 'ok' && text) {
						panel.setTitle(text);
					}
				}, this, false, panel.title);

				msg.getDialog().alignTo(panel.getEl(), 'tr-tr');
		    }
		},{
			id:'minus',
			handler: function(e, target, panel) {
				Ext.each(panel.findByType('container'),function(item) {

					if (!Ext.isEmpty(item.bbar)) {
						if (!item.getBottomToolbar().hidden) {
							item.getBottomToolbar().hide();
							panel.barsHidden = true;
						}
						else {
							item.getBottomToolbar().show();
							panel.barsHidden = false;
						}
					}

					if (!Ext.isEmpty(item.tbar)) {
						if (!item.getTopToolbar().hidden) {
							item.getTopToolbar().hide();
							panel.barsHidden = true;
						}
						else {
							item.getTopToolbar().show();
							panel.barsHidden = false;
						}
					}
						
					item.syncSize();

				});
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

		var cmp = Ext.getCmp("<?php echo $rd->getParameter('parentid'); ?>");

		// We need a state id from the cronkmanager, the parent id
		// is a good choice
		if (CE.stateuid) {
			Ext.apply(portal_config, {
				id: Ext.id(),
				stateId: CE.cmpid,
				stateful: true,

				// @todo The collapse event does not work?
				stateEvents: ['add', 'remove', 'titlechange', 'resize'],

				getState: function () {
					
					var d = new Array();

					this.items.each(function (col, cindex, l1) {

						crlist = {};

						col.items.each(function (cr, crindex, l2) {
							if (Cronk.Registry.get(cr.getId())) {
								var c = Cronk.Registry.get(cr.getId());
							
								c.height = cr.getHeight();
								c.barsHidden = cr.barsHidden;
								crlist[cr.getId()] = c;
							}
						}, this);

						d[cindex] = crlist;

					}, this);
					return {
						col: d,
						title: this.title
					}
				},

				applyState: function (state) {
					// Prevent multiple state restores
					if(this.appliedState)
						return true;
					else
						this.appliedState = true;
					// Defered execution
					(function() {
						if (state.col) {
							Ext.each(state.col, function (item, index, arry) {
								Ext.iterate(item, function (key, citem, o) {
									var c = citem;
									c.tools = tools;
									c.id = Ext.id(); // create new id, otherwise it might get ugly

									var cronk = Cronk.factory(c);
						
									PortalHandler.initPortlet(cronk);
									AppKit.log("adding ",cronk);	
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
	
		if (Ext.isDefined(CE.state)) {
			portal.applyState(CE.state);
		}
	
		CE.insert(0, portal);
		CE.doLayout();

	});	
</script>
