<?php $url = $t['url']; ?>
<?php if ($url) { ?>
<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	var newid = this.cmpid; 
	var domid = newid + '-dom';
	var stateuid = this.stateuid;
	
	// Create a new panel with a modified body element
	var config = {
		id: newid,
		listeners: {
			
			beforerender: function(ct) {
				this.bodyCfg = {
					tag: 'iframe',
					src: '<?php echo $url ?>',
					id: domid
				};
				
				Ext.EventManager.on(window, 'unload', function() {
					this.saveState();
				}, this);
				
				return true;
			}
			
		}
	};
	
	if (stateuid) {
		Ext.apply(config, {
			stateId: stateuid,
			stateEvents: ['unload'],
			stateful: true,
			
			getState: function() {
				var url = null;
				
				var e = this.body.dom;
				if (e.contentDocument) {
					url = e.contentWindow.location.href;
				}
				
				return {
					url: url
				};
			},
			
			applyState: function(state) {
				return true;
			}
		});
	}
	
	
	// Insert he element (no add, because reload results in multiple items)
	this.insert(0, new Ext.Panel(config));
	
	// Notify about changes
	this.doLayout();
	
});
</script>
<?php } ?>