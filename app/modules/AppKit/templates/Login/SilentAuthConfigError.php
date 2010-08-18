<script type="text/javascript">
	Ext.onReady(function() {
		AppKit.util.Layout.doLayout();
	});
</script>
<div class="simple-content-container simple-content-message">

	<h1><?php echo $tm->_('Login error (configuration)'); ?></h1>

	<p><?php echo $tm->_('auth.behaviour is not properly configured. You need at least one of the following attributes enabled:') ?></p>

	<ul>
		<li>modules.appkit.auth.behaviour.enable_dialog</li>
		<li>modules.appkit.auth.behaviour.enable_silent</li>
	</ul>

	<p><?php echo $tm->_('Please alter your config file and set one to \'true\' (%s).', null, null, array('app/modules/AppKit/config/auth.xml')) ?></p>

	<p><p><?php echo $tm->_('Don\'t forget to clear the agavi config cache after that.') ?></p></p>

</div>