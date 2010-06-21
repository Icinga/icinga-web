<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {

	var statusOverallRenderer = {
		prepareData: function(data, recordIndex, record) {
			data.state_org = data.state;
			data.state = Icinga.StatusData.wrapElement(data.type, data.state, data.count + ' {0}' );
			return data;
		}
	};

	var CE = this;

	var ds = new Ext.data.JsonStore({
		url: '<?php echo $ro->gen('cronks.statusOverall.json') ?>',
		storeId: 'overall-status-store'
	});

	ds.load();

	var statusOverallRefreshTask = {
		run: function() { ds.reload(); },
		interval: (1000*180)
	}

	AppKit.getTr().start(statusOverallRefreshTask);

	var p = new Ext.Panel({
		layout: 'fit',
		width: 400,
		height: 50,
		items: [{
			xtype: 'dataview',
			store: ds,
			autoHeight: true,
			prepareData: statusOverallRenderer.prepareData,
			itemSelector:'div.icinga-overall-status-item',

			listeners: {
				click: function(dview, index, node, e) {
					var d = dview.getStore().getAt(index).data;
					var template = 'icinga-' + d.type + '-template';
					var id = 'status-overall-' + d.type + d.state_org;
					var c = {
						
					}
				}
			},

			tpl: new Ext.XTemplate(
				'<tpl for=".">',
					'<tpl if="id==1">',
					'<div class="icinga-overall-status-icon silk-database" title="' + _('Hosts') + '"></div>',
					'</tpl>',
					'<tpl if="id==5">',
					'<div class="x-clear icinga-overall-status-spacer"></div>',
					'<div class="icinga-overall-status-icon silk-cog" title="' + _('Services') + '"></div>',
					'</tpl>',
					'<div class="icinga-overall-status-item" id="overall-status-{id}">',
					'<span>{state}</span>',
					'</div>',
				'</tpl>',
				'<div class="x-clear"></div>'
			)
		}]
	});
	
	CE.add(p);
	CE.doLayout();
});
</script>