<?php 
	
	$result =& $t['result'];

?>

<?php if ($result->getResultCount()) { ?>

<table cellpadding="0" cellspacing="0" border="1" class="icinga-data-table">

<tr>
	<th>Hostname</th>
	<th>Current state</th>
	<th>Last check</th>
	<th>Duration</th>
	<th>Output</th>
</tr>

<?php foreach ($result as $row) { ?>

<tr class="<?php echo AppKitHtmlHelper::Obj()->classAlternate('icinga-odd', 'icinga-even'); ?>">
	<td>
		<?php echo AppKitHtmlHelper::Obj()->LinkToRoute('icinga.hostDetail.single', $row->host_name, array('hostname' => $row->host_name)) ?>
		<!--  <small>(<?php // echo $row->host_alias; ?>)</small> -->
	</td>
	<td>
		<?php echo IcingaHostStateInfo::Create($row->host_current_state)->getCurrentStateAsHtml(); ?>
	</td>
	<td>
		<?php echo $tm->_d($row->host_last_check); ?>
	</td>
	<td>
		<?php echo AppKitDateUtil::durationToString( AppKitDateUtil::DateToDuration($row->host_last_state_change) ); ?>
	</td>
	<td>
		<?php echo $row->host_output; ?>
	</td>
</tr>

<?php } ?>

</table>

<?php } ?>