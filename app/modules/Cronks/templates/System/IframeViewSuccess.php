<?php 
	$url = $t['url'];
	$parentid = $rd->getParameter('parentid');
	$stateuid = $rd->getParameter('stateuid');
?>
<?php if ($url) { ?>
<script type="text/javascript">
(function() { 
	
	var cParent = Ext.getCmp('<?php echo $parentid; ?>');
	var stateuid = '<?php echo $stateuid; ?>';
	
	var newid = AppKit.Ext.genRandomId('iframe'); 
	var domid = newid + '-dom';
	
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
	cParent.insert(0, new Ext.Panel(config));
	
	// Notify about changes
	cParent.doLayout();
	
})();
</script>
<?php } ?>