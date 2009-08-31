<?php 
	$url = $t['url'];
	
	$htmlid = $rd->getParameter('htmlid');
	$newid = 'iframe-'. AppKitRandomUtil::genSimpleId(10);
?>
<?php if ($url) { ?>
<iframe id="<?php echo $newid; ?>" src="<?php echo $url; ?>">

</iframe>
<script type="text/javascript">
	var cmp = Ext.getCmp('<?php echo $htmlid; ?>');
	
	var iframe = Ext.get("<?php echo $newid; ?>");
	iframe.setWidth(cmp.getWidth());
	iframe.setHeight(cmp.getHeight());
	
	// Ext.Msg.alert('TEST', cmp.getHeight());
	
	Ext.getCmp('center-frame').on('resize', function(o) {
		iframe.setWidth(Ext.getCmp('<?php echo $htmlid; ?>').getWidth());
		iframe.setHeight(Ext.getCmp('<?php echo $htmlid; ?>').getHeight());
	});
</script>
<?php } ?>