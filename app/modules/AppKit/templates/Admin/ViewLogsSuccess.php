<?php 
	$collection	= $t['log_collection'];
	$pager		= $t['log_pager'];
	$map		= $t['log_levelmap'];
?>
<script type="text/javascript">
	Ext.onReady(function() {
		AppKit.util.Layout.doLayout();
	});
</script>
<?php if ($collection->count() > 0) { ?>
<table class="dataTable">
<tr>
	<th>Severity</th>
	<th>Message</th>
	<th>Timestamp</th>
	
</tr>
<?php foreach ($collection as $logEntry) { ?>
<?php 
	$loglevel = $map[ $logEntry->log_level ];
?>
<tr class="<?php echo AppKitHtmlHelper::Obj()->classAlternate('light', 'dark') ?>">
	<td class="loglevel-<?php echo $loglevel; ?>"><div class="loglevel-desc"><?php echo strtoupper($loglevel); ?></div></td>
	<td><div class="preformatted"><?php echo AppKitStringUtil::htmlPseudoPreformat( $logEntry->log_message ); ?></div></td>
	<td class="nowrap"><?php echo $logEntry->log_created; ?></td>
</tr>
<?php } ?>
</table>
<?php $pager instanceof AppKitDoctrinePager ? $pager->displayLayout() : null; ?>
<?php } ?>