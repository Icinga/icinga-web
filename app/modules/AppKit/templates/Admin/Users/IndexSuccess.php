<?php 
	/**
	 * @var Doctrine_Collection
	 */
	$collection = $t['user_collection'];
	
	/**
	 * @var AppKitDoctrinePager
	 */
	$pager = $t['user_pager'];
?>

<p>
<strong>List of available users</strong><br />
<span>(<?php echo AppKitHtmlHelper::Obj()->LinkToRoute('appkit.admin.users.edit', 'Create a new user', array('id' => 'new'))?>)</span>
</p>

<?php if ($collection && $collection->count() > 0) { ?>
<table class="dataTable">
	<tr>
		<th>&nbsp;</th>
		<th>Username</th>
		<th>Givenname</th>
		<th>Email address</th>
		<th>Created</th>
		<th>&nbsp;</th>
	</tr>
	
<?php foreach ($collection as $user) { ?>
	<tr class="<?php echo AppKitHtmlHelper::Obj()->classAlternate('light', 'dark'); ?>">
		<td><?php echo AppKitHtmlHelper::Obj()->LinkImageToRoute('appkit.admin.users', 'Toggle activity', $user->user_disabled ? 'icons.cross' : 'icons.tick', array('id' => $user->user_id, 'toggleActivity' => true), array(), $rd); ?></td>
		<td><?php echo $user->user_name; ?></td>
		<td><?php echo $user->givenName(); ?></td>
		<td><?php echo $user->user_email; ?></td>
		<td><?php echo $user->user_created; ?></td>
		<td>
			<?php echo AppKitHtmlHelper::Obj()->LinkImageToRoute('appkit.admin.users.edit', 'Edit user', 'icons.vcard_edit', array('id' => $user->user_id)); ?>
		</td>
	</tr>
<?php } ?>
	
</table>

<?php $pager instanceof AppKitDoctrinePager ? $pager->displayLayout() : null; ?>

<?php } else { ?>

<?php } ?>