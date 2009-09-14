<?php 
	$url = $t['url'];
	$htmlid = $rd->getParameter('htmlid');
	$newid = 'iframe-'. AppKitRandomUtil::genSimpleId(10);
?>
<?php if ($url) { ?>

</iframe>
<script type="text/javascript">

(function() { 
	var cParent = Ext.getCmp('<?php echo $htmlid; ?>');
	
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
	
	cParent.add(iframe);
	cParent.doLayout();
	
})();
	
</script>
<?php } ?>