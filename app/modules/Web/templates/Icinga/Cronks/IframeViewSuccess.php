<?php 
	$url = $rd->getParameter('url');
	$htmlid = $rd->getParameter('htmlid');
?>
<script type="text/javascript">
	alert("<?php echo $htmlid; ?>");
	var cmp = Ext.getCmp("<?php echo $htmlid; ?>");
	cmp.add(new Ext.Panel({title: 'test', html: 'Das ist ein test'}));
	cmp.add({html: 'test'});
	// container.doLayout();
</script>