<?php 

$role = $t['role'];

?>
<?php if ($role instanceof NsmRole) { ?>

	<div>
		<div id="group_data">
		
			<h4>Basic data</h4>
		
			<form action="<?php echo $ro->gen(null); ?>" method="post">
		
			<table class="editTable">
			
				<tr>
					<td class="key">Name:</td>
					<td class="val"><?php echo AppKitFormElement::create('text', 'role_name', $role->role_name); ?></td>
				</tr>
			
				<tr>
					<td class="key">Description:</td>
					<td class="val"><?php echo AppKitTextboxElement::create('role_description', $role->role_description); ?></td>
				</tr>
			
				<tr>
					<td class="key">&#160;</td>
					<td class="val"><?php echo AppKitCheckboxElement::create('role_disabled', 1, $role->role_disabled, 'Role is disabled'); ?></td>
				</tr>
			
				<tr>
					<td class="space" colspan="2">&#160;</td>
				</tr>
			
				<tr>
					<td class="key">Created:</td>
					<td class="val"><?php echo $role->role_created; ?></td>
				</tr>
			
				<tr>
					<td class="key">Updated:</td>
					<td class="val"><?php echo $role->role_modified; ?></td>
				</tr>
			
			</table>
		
			<div id="group_members" style="margin-top: 10px">
			
			<h4>Memberships</h4>
			<div>
			<?php if ($role->NsmUser->count() > 0) { ?>
				<table class="dataTable">
					<tr>
						<th>Username</th>
						<th>Given name</th>
						<th>&#160;</th>
					</tr>
				<?php foreach ($role->NsmUser as $user) { ?>
					<tr>
						<td><?php echo $user->user_name; ?></td>
						<td><?php echo $user->givenName(); ?></td>
						<td><?php echo AppKitHtmlHelper::Obj()->LinkImageToRoute('appkit.admin.users.edit', 'Goto the user', $user->user_disabled == true ? 'icons.user_delete' : 'icons.user_go', array('id' => $user->user_id)); ?></td>
					</tr>
				<?php } ?>
				</table>
			<?php } else { ?>
				<i>Sorry, this role contains no members!</i>
			<?php } ?>
			</div>
			</div>
			
			<div class="submit">
				<?php echo AppKitFormElement::create('submit', 'submit', 'Update')?>
			</div>
			
			</form>
		</div>
	
		
	</div>


<?php } ?>