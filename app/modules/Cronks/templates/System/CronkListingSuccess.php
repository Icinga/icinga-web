<?php
	$parentid = $rd->getParameter('parentid');
?>
<!--<div class="cronk-data-view" id="<?php echo $parentid; ?>"></div>-->

<script type="text/javascript">

(function() {

	var CronkListing = function() {

		return {
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
		}
		
	}(); 
	
	// Our store to retrieve the cronks
	var store = new Ext.data.JsonStore({
	    url: "<?php echo $ro->gen('icinga.cronks.crlisting.json'); ?>",
	    root: 'cronks',
	    fields: [
	        'name', 'id', 'description', 'image', 'parameter'
	    ]
	});
	
	// Load the data
	store.load();
	
	// Template to display the cronks
	var tpl = new Ext.XTemplate(
	    '<tpl for=".">',
	    	'<div class="cronk-preview" id="{name}">',
        	'<div class="thumb"><img ext:qtip="{description}" src="{image}"></div>',
        	'<span class="x-editable">{name}</span>',
        	'</div>',
	    '</tpl>',
	    '<div class="x-clear"></div>'
	);
	
	// The dataview container
	var view = new Ext.DataView({
        store: store,
        tpl: tpl,
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
	
	var cmp = Ext.getCmp("<?php echo $parentid; ?>");
	cmp.add(view);
		
})();


</script>
