<?php 

$user = $t['user'];
$roles = $t['roles'];

?>
<?php if ($user instanceof NsmUser) { ?>

<form action="<?php echo $ro->gen(null); ?>" method="post">

<table class="structural">
<tr><td>

<table class="editTable">

	<tr>
		<td class="key">Username:</td>
		<td class="val"><?php echo AppKitFormElement::create('text', 'user_name', $user->user_name); ?></td>
	</tr>
	
	<tr>
		<td class="key">Lastname:</td>
		<td class="val"><?php echo AppKitFormElement::create('text', 'user_lastname', $user->user_lastname); ?></td>
	</tr>
	
	<tr>
		<td class="key">Firstname:</td>
		<td class="val"><?php echo AppKitFormElement::create('text', 'user_firstname', $user->user_firstname); ?></td>
	</tr>
	
	<tr>
		<td class="key">Email:</td>
		<td class="val"><?php echo AppKitFormElement::create('text', 'user_email', $user->user_email); ?></td>
	</tr>
	
	<tr>
		<td class="key">Disabled:</td>
		<td class="val"><?php echo AppKitCheckboxElement::create('user_disabled', 1, $user->user_disabled ? true : false, 'User is disabled'); ?></td>
	</tr>
	
	<tr>
		<td class="space" colspan="2">&#160;</td>
	</tr>

	<tr>
		<td class="key">Password:</td>
		<td class="val"><?php echo AppKitFormElement::create('password', 'password', null); ?></td>
	</tr>
	
	<tr>
		<td class="key">Password (Validation):</td>
		<td class="val"><?php echo AppKitFormElement::create('password', 'password_validate', null); ?></td>
	</tr>

	<tr>
		<td class="space" colspan="2">&#160;</td>
	</tr>
	
	<tr>
		<td class="key">Created:</td>
		<td class="val"><?php echo $user->user_created; ?></td>
	</tr>
	
	<tr>
		<td class="key">Updated:</td>
		<td class="val"><?php echo $user->user_modified; ?></td>
	</tr>

</table>

<div class="submit">
	<?php echo AppKitFormElement::create('submit', 'submit', 'Update')?>
</div>

</td>
<td>

<table class="editTable">
	<tr>
		<td class="key">Group membership:</td>
	</tr>
	<tr>
		<td class="val">
		
			<div class="frameLeft borderRight">
			<?php
				$source = new AppKitSelectDoctrineSource(
					$roles,
					$user->NsmRole,
					'role_id',
					'role_name'
				);
				
				echo AppKitSelectCheckboxElement::create('userroles[]', 'Edit the userroles', $source)->setMultiple();
			?>
			</div>
		
			<div class="frameLeft">
			<?php if ($user->NsmRole->count()) { ?>
			<?php foreach ($user->NsmRole as $role) { ?>
			<div class="middlevalign">
				<?php echo AppKitHtmlHelper::Obj()->Image($role->role_disabled ? 'icons.group_delete' : 'icons.group', 'Assigned to role: '. $role->role_name); ?>
				<span><?php echo AppKitHtmlHelper::Obj()->LinkToRoute('appkit.admin.groups.edit', sprintf('%s (%s)', $role->role_name, $role->role_description), array('id' => $role->role_id)); ?></span>
			</div>
				
			<?php } ?>
			<?php } else { ?>
				<i>No roles were assigned to this user</i>
			<?php } ?>
			</div>
		</td>
	</tr>
	
</table>

<?php /*
<table class="editTable">
	<tr>
		<td colspan="2" class="key">Principal</td> 
	</tr>

	<tr>
		<td colspan="2" class="val">
		
			<!-- Edit frame -->
			<div id="principal_edit_frame"></div>
			
			<!-- Load the principal editor the corresponding principal -->
			<script type="text/javascript">
			<!-- // <![CDATA[

			Ext.onReady(function() {
				var ele = Ext.get('principal_edit_frame');
				if (ele) {
					ele.getUpdater().setDefaultUrl({
						url: '<?php echo $ro->gen("appkit.admin.principaledit", array("principal" => $user->NsmPrincipal->principal_id)); ?>',
						scripts: true
					});

					ele.getUpdater().refresh();
				}
			});
            
			// ]]> -->
			</script>
		
		</td>
	</tr>
	
</table>

*/ ?>

</td></tr>
</table>


<?php echo AppKitHiddenElement::create('id', $user->user_id ? $user->user_id : 'new')?>


</form>


<?php } ?>