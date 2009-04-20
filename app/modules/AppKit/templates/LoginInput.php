<?php if ($t['error']) { ?>
	<p class="error"><?php echo $t['error'] ?></p>
<?php } ?>

<form action="<?php echo $ro->gen('appkit.login') ?>" method="post">

<div class="form_login">

<table class="editTable">

	<tr>
		<td colspan="2" class="key">Login</td>
	</tr>

	<tr>
		<td class="key">Username</td>
		<td class="val"><?php echo AppKitFormElement::create('text', 'username', null, 'Username to login')?></td>
	</tr>

	<tr>
		<td class="key">Password</td>
		<td class="val"><?php echo AppKitFormElement::create('password', 'password', null, 'Password to login')?></td>
	</tr>
	
	<tr>
		<td colspan="2" class="space">&#160;</td>
	</tr>
	
	<tr>
		<td colspan="2" align="right">
			<div class="submit">
				<?php echo AppKitFormElement::create('submit', null, 'Login', 'Click here to login')?>
			</div>
		</td>
	</tr>

</table>


</div>

</form>