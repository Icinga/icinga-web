<?php
	$parentid = $rd->getParameter('parentid');
?>
<script type="text/javascript">

(function() {

	var CronkListing = function() {

		var parentCmp = null;
		var template  = null;
		var out = {};
		
		Ext.apply(out, {
			
			getBaseUrl : function() {
				return "<?php echo $ro->gen('icinga.cronks.crlisting.json'); ?>";
			},
			
			setParentCmp : function(cmp) {
				parentCmp = cmp;
			},
			
			getParentCmp : function() {
				return parentCmp;
			},
			
			getStore : function () {
				return new Ext.data.JsonStore({
					autoDestroy: true,
				    url: CronkListing.getBaseUrl(),
				    root: 'cronks',
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
			
			getNewView : function(cat) {
				
				var s = CronkListing.getStore();
				
				s.baseParams = {
					type: 'cronks',
					cat: cat
				};
				
				s.reload();
				
				var v = new Ext.DataView({
			        store: s,
			        tpl: CronkListing.getTemplate(),
			        autoHeight:true,
			        multiSelect: true,
			        overClass:'x-view-over',
			        itemSelector:'div.cronk-preview',
			        emptyText: 'No data',
			       	cls: 'cronk-data-view',
			        
			        // Create the drag zone
			        listeners: {
			            render: CronkListing.initCronkDragZone,
			            dblclick: CronkListing.dblClickHandler
			        }
			        
			        
			    });
			    
			    return v;
			},
			
			addListing : function (title, cat) {
				parentCmp.add({
					title: title,
					border: false,
					defaults: { border: false },
					items: [ CronkListing.getNewView(cat) ]
				});
				
				parentCmp.doLayout();
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
				
				var panel = AppKit.Ext.CronkMgr.create({
					parentid: AppKit.Ext.genRandomId('cronk-'),
					title: record.data['name'],
					crname: record.data.id,
					loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
					closable: true,
					layout: 'fit',
					params: record.data.parameter
				});
				
				var tabPanel = Ext.getCmp('cronk-tabs');
				
				if (tabPanel) {
					tabPanel.add(panel);
					tabPanel.setActiveTab(panel);
				}
			}
			
		});
		
		return out;
		
	}();
	
	CronkListing.setParentCmp(Ext.getCmp("<?php echo $parentid; ?>"));
	// CronkListing.initListing();
	
	Ext.Ajax.request({
		url: CronkListing.getBaseUrl(),
		params: { type: 'cat' },
		success: function (r, o) {
			var d = Ext.decode(r.responseText);	
			
			if (d.categories) {
				var act = null;
				var i = 0;
				Ext.iterate(d.categories, function(k,v) {
					
					if (v.active && v.active == true) {
						act = i;
					}
					
					CronkListing.addListing(v.title || 'untitled', k);
					
					i++;
				});
				
				if (act) {
					CronkListing.getParentCmp().getLayout().setActiveItem(act);
				}
			}
			
		},
		failure: function (r, o) {
			AppKit.Ext.notifyMessage('Ajax Error', 'Could not load the categories (CronkList)');
		}
	});
		
})();


</script>
