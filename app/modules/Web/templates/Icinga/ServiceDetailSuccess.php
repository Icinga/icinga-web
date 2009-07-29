<?php 
	
	$result =& $t['result'];
	$hosts  = array ();
?>

<?php if ($result->getResultCount()) { ?>

<table cellpadding="0" cellspacing="0" border="1" class="icinga-data-table">

<tr>
	<th>Hostname</th>
	<th>Servicename</th>
	<th>Status</th>
	<th>Last check</th>
	<th>Duration</th>
	<th>Output</th>
</tr>

<?php foreach ($result as $row) { ?>

<tr class="<?php echo AppKitHtmlHelper::Obj()->classAlternate('icinga-odd', 'icinga-even'); ?>">
	<td>
	<?php
	
		if (array_key_exists($row->host_name, $hosts)) {
			echo '&#160';	
		}
		else {
			$hosts[ $row->host_name ] = true;
			echo AppKitHtmlHelper::Obj()->LinkToRoute('icinga.hostDetail.single', $row->host_name, array('hostname' => $row->host_name));
		}
	
	?>
	</td>
	<td>
	<?php
		echo AppKitHtmlHelper::Obj()->LinkToRoute('icinga.serviceDetail.single', $row->service_name, array(
			'hostname'		=> $row->host_name,
			'servicename'	=> $row->service_name
		)); 
	?>
	</td>
	<td><?php echo IcingaServiceStateInfo::Create($row->service_current_state)->getCurrentStateAsHtml(); ?></td>
	<td>
		<?php echo $tm->_d($row->service_last_check); ?>
	</td>
	<td>
		<?php echo AppKitDateUtil::durationToString( AppKitDateUtil::DateToDuration($row->service_last_state_change) ); ?>
	</td>
	<td>
		<?php echo $row->service_output; ?>
	</td>
</tr>

<?php } ?>

</table>

<?php } ?>