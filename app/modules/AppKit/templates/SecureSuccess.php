<p>You do not have sufficient credentials to access this page
(<?php echo $t['requested_action']; ?>, <?php echo $t['requested_module']; ?>).</p>

<form name="logout" action="<?php echo $ro->gen('appkit.logout') ?>" method="post">
<p>You are currently logged in as
<strong><?php echo $us->getAttribute('userobj')->givenName(); ?></strong>,
press the button to logout!</p>

<div class="submit">
<?php echo AppKitFormElement::create('submit', null, 'logout'); ?>
</div>

<?php echo AppKitHiddenElement::create('logout', true); ?>
</form>