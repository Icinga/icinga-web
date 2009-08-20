<div id="icinga-cronk-portal-frame"></div>
<script type="text/javascript">

	var ele = Ext.get('icinga-cronk-portal-frame').load({
		url:		'<?php echo $ro->gen('icinga.cronks.loader', array('cronk' => 'portal')); ?>',
		params:		{ 'p[htmlid]': 'icinga-cronk-portal' },
		scripts:	true
	});

</script>