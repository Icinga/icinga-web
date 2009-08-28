<?php 
	$url = $t['url'];
	
	$htmlid = $rd->getParameter('htmlid');
	$newid = 'iframe-'. AppKitRandomUtil::genSimpleId(10);
?>
<?php if ($url) { ?>
<iframe id="<?php echo $newid; ?>" src="<?php echo $url; ?>">

</iframe>
<script type="text/javascript">
	var cmp = Ext.getCmp('center-frame');
	
	var iframe = Ext.get("<?php echo $newid; ?>");
	iframe.setWidth(cmp.getWidth());
	iframe.setHeight(cmp.getHeight());
	
	Ext.getCmp('center-frame').on('resize', function(o) {
		iframe.setWidth(Ext.getCmp('center-frame').getWidth());
		iframe.setHeight(Ext.getCmp('center-frame').getHeight());
	});

</script>
<?php } ?>