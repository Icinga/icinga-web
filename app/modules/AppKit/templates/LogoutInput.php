<form name="logout" action="<?php echo $ro->gen('modules.appkit.logout') ?>" method="post">
<p>You are currently logged in as
<strong><?php echo $us->getAttribute('userobj')->givenName(); ?></strong>,
press the button to logout!</p>

<div class="submit">
<?php echo AppKitFormElement::create('submit', null, 'logout'); ?>
</div>

<?php echo AppKitHiddenElement::create('logout', true); ?>
</form>