<?php 
    $t['current'] = $ro->gen(null);
?>
<script type="text/javascript">
Ext.onReady(function() {
	var m = new AppKit.util.LogoutMachine(<?php echo json_encode($t); ?>);
    m.doLogout();
});
</script>
<h1>Bye ...</h1>
<p style="font-size: 10pt;">
	You've beed logged out successfully!
	You can go to the <a href="<?php echo $t['url']; ?>">loginpage</a>
	to authentificate again.
</p>
