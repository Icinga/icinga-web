<?php 
	$url = $t['url'];
	$parentid = $rd->getParameter('parentid');
	$newid = 'iframe-'. AppKitRandomUtil::genSimpleId(10);
	$stateuid = $rd->getParameter('stateuid');
?>
<?php if ($url) { ?>
<script type="text/javascript">
(function() { 
	var cParent = Ext.getCmp('<?php echo $parentid; ?>');
	var stateuid = '<?php echo $stateuid; ?>';
	
	// Create a new panel with a modified body element
	var config = {
		id: '<?php echo $newid; ?>',
		listeners: {
			
			beforerender: function(ct) {
				this.bodyCfg = {
					tag: 'iframe',
					src: '<?php echo $url ?>'
				};
			}
			
		}
	};
	
	if (stateuid) {
		Ext.apply(config, {
			stateful: true,
			stateId: stateuid,
			stateEvents: ['resize'],
			
			getState: function() {
				return {
					height: this.getHeight()
				}
			},
			
			applyState: function(state) {
				if (state.height) {
					this.on('added', function(panel, ownerCt, index)  {
						
						var container = panel.findParentByType('portlet');
						if (container && container.resizer) {
							container.setHeight(state.height);
						}
						
						return true;
						
					}, null, { single: true });
				}
			}
		});
	}
	
	// Insert the element (no add, because reload results in multiple items)
	cParent.insert(0, new Ext.Panel(config));
	
	// Notify about changes
	cParent.doLayout();
	
})();
</script>
<?php } ?>