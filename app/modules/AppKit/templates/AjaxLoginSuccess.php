<?php 
	$htmlid = AppKitRandomUtil::genSimpleId(10);
?>
<div id="<?php echo $htmlid; ?>"></div>
<script type="text/javascript">
(function() {
Ext.onReady(function() {

	var oWin = new Ext.Window({
		title: '<?php echo $tm->_("Login"); ?>',
		closable: true,
		modal: true,
		renderTo: Ext.getBody(),
		width: 300,
		layout: 'form',
		defaultType: 'textfield',
		bodyStyle: 'padding: 5px',
		
		items: [{
			fieldLabel: '<?php echo $tm->_("User"); ?>',
			name: 'user'
		}, {
			fieldLabel: '<?php echo $tm->_("Password"); ?>',
			name: 'password',
			inputType: 'password'
		}],
		
		buttons: [
			{
				text: '<?php echo $tm->_("Try"); ?>',
				handler: function(o, e) {
					Ext.Msg.alert("Test", "Try login ... ");
				}
			}, {
				text: '<?php echo $tm->_("Abort"); ?>',
				handler: function(o, e) {
					oWin.hide();
				}
			}
		]
		
	});
	
	<?php if ($us->isAuthenticated() !== true) { ?>
	oWin.show();
	<?php } ?>

});
})();
</script>