<?php 
	
	$result =& $t['result'];

?>

<table>

<?php foreach ($result as $row) { ?>

<tr>
	<td><?php echo $row->host_name; ?></td>
</tr>

<?php } ?>

</table>