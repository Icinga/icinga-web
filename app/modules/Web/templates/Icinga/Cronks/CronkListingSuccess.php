<?php
	$htmlid = $rd->getParameter('htmlid');
?>
<div class="cronk-data-view" id="<?php echo $htmlid; ?>"></div>

<script type="text/javascript">

function loadCronkDataView() {
	
	// Our store to retrieve the cronks
	var store = new Ext.data.JsonStore({
	    url: '<?php echo $ro->gen('icinga.cronks.crlisting.json'); ?>',
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
        	'<div class="thumb"><img src="{image}"></div>',
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
        emptyText: 'No images to display',
        
        // Create the drag zone
        listeners: {
            render: initCronkDragZone
        }
        
        
    });
	
	view.render('<?php echo $htmlid; ?>');	
	
	function initCronkDragZone(v) {
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
	}
}

loadCronkDataView();

</script>
