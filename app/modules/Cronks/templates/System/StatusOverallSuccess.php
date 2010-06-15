<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {

	var CE = this;

	var ds = new Ext.data.JsonStore({
		url: '<?php echo $ro->gen('cronks.statusOverall.json') ?>',
		storeId: 'overall-status-store'
	});

	ds.load();

	var p = new Ext.Panel({
		title: 'LAOLA',
		layout: 'fit',
		width: 400,
		height: 200,
		items: [{
			xtype: 'dataview',
			store: ds,
			autoHeight: true,
			tpl: new Ext.XTemplate(
				'<tpl for=".">',
					'<tpl if="id==4">',
					'<div class="x-clear"></div>',
					'</tpl>',

					'<div class="icinga-overall-status-item" id="overall-status-{id}">',
					'<span>({id})</span>',
					'<span>{state}: </span>',
					'<span>{count}</span></div>',
				'</tpl>'
			)
		}]
	});
	
	CE.add(p);
	CE.doLayout();
});
</script>