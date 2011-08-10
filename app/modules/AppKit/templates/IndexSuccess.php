
<h4>Welcome to the ICINGA Appkit Application Suite</h4>

<p>ICINGA AppKit is a application framework build on to of Agavi to implement as fast as
possible applications within the same context.</p>

<p>The first implemented module is the NETWAYSGrapher, a versatile, flash-driven plotting application to display
performance data from other applications (e.g. NAGIOS).</p>

<?php if (!$us->isAuthenticated()) { ?>
<p>We've noticed that you are not logged in, you can do this right now at the <a href="<?php echo $ro->get('appkit.login'); ?>">loginpage</a>.</p>
<?php } ?>
