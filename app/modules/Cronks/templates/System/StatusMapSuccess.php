<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
	var CE = this;

	var tryToDrawStatusMap = function() {
		try {
			if(Ext.isDefined(JitStatusMap)) 
				drawMap();	
			else
				tryToDrawStatusMap.defer(200);
		} catch(e) {
			tryToDrawStatusMap.defer(200);
		}
	}
	
	var drawMap = function() {
		var statusMap = new JitStatusMap({
			url: "<?php echo $ro->gen('cronks.statusMap.json'); ?>",
			parentId: CE.parentid
		});
	}

	tryToDrawStatusMap();
});
</script>
