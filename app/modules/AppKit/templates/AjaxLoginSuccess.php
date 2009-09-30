<?php 
	$htmlid = AppKitRandomUtil::genSimpleId(10);
?>
<?php if ($us->isAuthenticated() !== true) { ?>
<div id="<?php echo $htmlid; ?>"></div>
<script type="text/javascript">

Ext.onReady(function() {

	oWin

});

</script>
<?php } ?>