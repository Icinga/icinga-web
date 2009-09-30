<?php 
	$url = $t['url'];
	$parentid = $rd->getParameter('parentid');
	$newid = 'iframe-'. AppKitRandomUtil::genSimpleId(10);
?>
<?php if ($url) { ?>

</iframe>
<script type="text/javascript">

(function() { 
	var cParent = Ext.getCmp('<?php echo $parentid; ?>');
	
	// Create a new panel with a modified body element
	var iframe = new Ext.Panel({
		id: '<?php echo $newid; ?>',
		listeners: {
			
			beforerender: function(ct) {
				this.bodyCfg = {
					tag: 'iframe',
					src: '<?php echo $url ?>'
				};
			}
			
		}
	});
	
	// Insert the element (no add, because reload results in multiple items)
	cParent.insert(0, iframe);
	
	// Notify about changes
	cParent.doLayout();
	
})();
	
</script>
<?php } ?>