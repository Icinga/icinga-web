<script type="text/javascript">
<!-- /* <![CDATA[ */
YAHOO.util.Event.onContentReady('cronk-1', function() {
AppKit.ajaxHtmlRequest('<?php echo $ro->gen(
	'icinga.cronks.loader', 
	array('cronk' => 'viewProc', 'p' => array('template' => 'icinga-test-template')
	));  ?>', 'cronk-1');
})
/* ]]> */ -->
</script>

<div id="cronk-1"></div>