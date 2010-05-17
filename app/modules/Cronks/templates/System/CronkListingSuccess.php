<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {

	// Local copy of this
	var CE = this;

	// Listing object
	var CronkListing = function() {
		var template  = null;
		
		var c = {
			layout: 'accordion',
			layoutConfig: {
				animate: true,
				renderHidden: false,
				hideCollapseTool: true,
				fill: true
			},
			
			autoScroll: true,
			border: false,
			
			defaults: { border: false }
		};
		
		var stateuid = 'cronk-listing-panel';
		
		if (stateuid) {
			Ext.apply(c, {
				
				id: stateuid,
				stateId: stateuid,
				stateEvents: ['collapse'],
				stateful: true,
				bubbleEvents: [],
				
				defaults: {
					listeners: {
						collapse: function() {
							addCmp.saveState();
						}
					}
				},
				
				applyState: function(state) {
					if (state && "active_tab" in state && state.active_tab >= 0) {
						this.active_tab = state.active_tab;
					}
				},
				
				getState: function() {
					var active = this.getLayout().activeItem, i;
					this.items.each(function(item, index, l) {
						if (item == active) {
							i = index;
						}
					});
					
					if (typeof(i) !== "undefined" && i>=0) {
						return { active_tab: i }
					}
				},
				
				listeners: {
					beforecollapse: function() {
						return false;
					}
				}
				
			});
		}
		
		var addCmp = new Ext.Panel(c);
		
		CE.add(addCmp);
		
		return {
			
			setActiveItem : function(id) {
				addCmp.getLayout().setActiveItem(id);
			},
			
			applyActiveItem : function() {
				var c = this.getFrameCmp();
				if (c.active_tab) {
					this.setActiveItem(c.active_tab);
					return true;
				}
				return false;
			},
			
			getBaseUrl : function() {
				return "<?php echo $ro->gen('cronks.crlisting.json'); ?>";
			},
			
			getParentCmp : function() {
				return CE.getParent();
			},
			
			getFrameCmp : function() {
				return addCmp;
			},
			
			getStore : function (json) {
				return new Ext.data.JsonStore({
					autoDestroy: true,
					autoLoad: true,
				    totalProperty: 'resultCount',
				    root: 'resultRow',
				    data: json,
				    fields: [
				        'name', 'id', 'description', 'image', 'parameter'
				    ]
				});
			},
			
			getTemplate : function () {
				
				if (!template) {
					template = new Ext.XTemplate(
					    '<tpl for=".">',
					    	'<div class="cronk-preview" id="{name}">',
				        	'<div class="thumb"><img ext:qtip="{description}" src="{image}"></div>',
				        	'<span class="x-editable">{name}</span>',
				        	'</div>',
					    '</tpl>',
					    '<div class="x-clear"></div>'
					);
				}
				
				return template;
				
			},
			
			getNewView : function(json) {
				
				var s = CronkListing.getStore(json);

				return new Ext.DataView({
			        store: s,
			        tpl: CronkListing.getTemplate(),
			        overClass:'x-view-over',
			        itemSelector:'div.cronk-preview',
			        emptyText: 'No data',
			       	cls: 'cronk-data-view',
			        border: false,
			        
			        // Create the drag zone
			        listeners: {
			            render: CronkListing.initCronkDragZone,
			            dblclick: CronkListing.dblClickHandler
			        } 
			    });
			},
			
			addListing : function (title, json) {
				addCmp.add({
					title: title,
					items: CronkListing.getNewView(json),
					border: false
				});
				
				addCmp.doLayout();
			},
			
			initCronkDragZone : function (v) {
				v.dragZone = new Ext.dd.DragZone(v.getEl(), {
					ddGroup: 'cronk',
					
					getDragData: function(e) {
					var sourceEl = e.getTarget(v.itemSelector, 10);

			            if (sourceEl) {
			                d = sourceEl.cloneNode(true);
			                d.id = Ext.id();
			                return v.dragData = {
			                    sourceEl: sourceEl,
			                    repairXY: Ext.fly(sourceEl).getXY(),
			                    ddel: d,
			                    dragData: v.getRecord(sourceEl).data
			                }
			
			            }
					
					},
					
					getRepairXY: function() {
						return this.dragData.repairXY;
					}
				
				});
			},

			dblClickHandler: function(oView, index, node, e) {
				var record = oView.getStore().getAt(index);
				
				var tabPanel = Ext.getCmp('cronk-tabs');
				
				if (tabPanel) {
					var panel = tabPanel.add({
						xtype: 'cronk',
						title: record.data['name'],
						crname: record.data.id,
						closable: true,
						params: record.data.parameter
					});
					
					tabPanel.setActiveTab(panel);
				}
			},
			
			go : function() {
				Ext.Ajax.request({
					url: this.getBaseUrl(),
					success: function (r, o) {

						var d = Ext.decode(r.responseText);	
						if (Ext.isDefined(d.cat) && d.cat.resultSuccess == true) {
							try {
								var i = 0,
								act = -1;
								Ext.iterate(d.cat.resultRow, function(k, v) {
										CronkListing.addListing(v.title || 'untitled', d.cronks[k]);
										if (Ext.isDefined(v.active) && v.active == true) act=i;
										i++;
								});
								
								if (!CronkListing.applyActiveItem() && act) {
										CE.getParent().getLayout().setActiveItem(act);
								}
							}
							catch (e) {
								// DO NOTHING
							}
							
							CE.doLayout();
						}
					},
					failure: function (r, o) {
						var str = String.format(
							_('Could not load the cronk listing, following error occured: {0} ({1})'),
							r.status,
							r.statusText
						);
						
						parentcmp.add({
							layout: 'fit',
							html: str
						});
						parentcmp.doLayout();
						
						AppKit.notifyMessage('Ajax Error', str, { waitTime: 20 });
					}
					
				});
				
			}
			
		};
	}();

	CronkListing.go();
	
//	var parentCmp = Ext.getCmp("<?php echo $parentid; ?>");
//	CronkListing.setParentCmp(parentCmp);
//	
//	Ext.Ajax.request({
//		url: CronkListing.getBaseUrl(),
//		params: { type: 'cat' },
//		success: function (r, o) {
//			var d = Ext.decode(r.responseText);	
//			
//			if (d.categories) {
//				var act = null;
//				var i = 0;
//				Ext.iterate(d.categories, function(k,v) {
//					
//					if (v.active && v.active == true) {
//						act = i;
//					}
//					
//					CronkListing.addListing(v.title || 'untitled', k);
//					i++;
//				});
//				
//				
//			if (!CronkListing.applyActiveItem() && act) {
//					CronkListing.getParentCmp().getLayout().setActiveItem(act);
//				}
//			}
//			
//		},
//		failure: function (r, o) {
//			AppKit.notifyMessage('Ajax Error', 'Could not load the categories (CronkList)');
//		}
//	});
		
});


</script>
