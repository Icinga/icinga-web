<script type="text/javascript">
	Ext.onReady(function() {
		AppKit.util.Layout.doLayout();
	});
</script>
<div class="simple-content-container simple-content-message">

	<h1><?php echo $tm->_('Login error'); ?></h1>

	<p><?php echo $tm->_('Sorry, you could not be authenticated for icinga-web.') ?></p>

	<p><?php echo $tm->_('Please contact your system admin if you think this is not common.') ?></p>

</div>