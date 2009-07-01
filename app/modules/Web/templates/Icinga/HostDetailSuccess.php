<?php 
	
	$result =& $t['result'];

?>

<table border="1">

<?php foreach ($result as $row) { ?>

<tr>
	<td>
		<?php echo $row->host_name; ?><br />
		<small>(<?php echo $row->host_alias; ?>)</small>
	</td>
	<td>
		<?php echo $row->host_current_state; ?><br />
	</td>
</tr>

<?php } ?>

</table>