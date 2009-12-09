<?php 
	$eid = AppKitRandomUtil::genSimpleId(10, 'principaledit-');
?>
<div id="<?php echo $eid; ?>"></div>
<script type="text/javascript">

(function() {

	var eid = '<?php echo $eid?>';

	var p = new Ext.Panel({
		renderTo: eid,
		layout: 'form',
		width: 400,
		bodyStyle: 'padding: 4px',
		
		tbar: [{
			text: '<?php echo $tm->_("add"); ?>',
			iconCls: 'silk-add',
			menu: [
			
			]
		}]
	});
	
	p.doLayout();
	
	
})();

</script>