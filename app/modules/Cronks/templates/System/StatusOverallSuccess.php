<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {

	var statusOverallRenderer = {
		prepareData: function(data, recordIndex, record) {
			data.state_org = data.state;
			
			if (data.count == 0) {
				data.state = Icinga.StatusData.wrapElement(data.type, data.state, data.count + ' {0}', Icinga.DEFAULTS.STATUS_DATA.servicestatusClass[100]);
			}
			else {
				data.state = Icinga.StatusData.wrapElement(data.type, data.state, data.count + ' {0}');
			}
			
			return data;
		}
	};

	var CE = this;

	var ds = new Ext.data.JsonStore({
		url: '<?php echo $ro->gen('cronks.statusOverall.json') ?>',
		storeId: 'overall-status-store'
	});

	ds.load();

	var interval = <?php echo $us->getPrefVal('org.icinga.grid.refreshTime', AgaviConfig::get('modules.cronks.grid.refreshTime', 120)); ?>;
	
	var statusOverallRefreshTask = {
		run: function() { ds.reload(); },
		interval: (1000*interval)
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
			openCronkFn: new Ext.util.DelayedTask(function(cronk,filter) {
			    Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
			}),
			listeners: {
				click: function(dview, index, node, e) {
					var d = dview.getStore().getAt(index).data;

					var params = {
						template: 'icinga-' + d.type + '-template',
						module: 'Cronks',
						action: 'System.ViewProc'
					};

					var filter = {};

					// 100 is the summary of all (== no filter)
					if (d.state_org < 100) {
						filter['f[' + d.type + '_status-value]'] = d.state_org;
						filter['f[' + d.type + '_status-operator]'] = 50;
					}

					var id = 'status-overall-grid' + d.type + '-' + d.state_org;

					var cronk = {
						parentid: id,
						id: id + '-frame',
						stateuid: id + '-persistent',
						title: String.format('{0}s {1}', d.type, Icinga.StatusData.simpleText(d.type, d.state_org).toLowerCase()),
						crname: 'gridProc',
						closable: true,
						params: params
					};

					dview.openCronkFn.delay(300,null,null,[cronk, filter]);
				}
			},

			tpl: new Ext.XTemplate(
				'<div class="icinga-overall-status-container clearfix">',
				'<tpl for=".">',
					'<tpl if="id==1">',
					'<div class="icinga-overall-status-icon icinga-icon-host" title="' + _('Hosts') + '"></div>',
					'</tpl>',
					'<tpl if="id==5">',
					'<div class="x-clear icinga-overall-status-spacer"></div>',
					'<div class="icinga-overall-status-icon icinga-icon-service" title="' + _('Services') + '"></div>',
					'</tpl>',
					'<div class="icinga-overall-status-item" id="overall-status-{id}">',
					'<span>{state}</span>',
					'</div>',
				'</tpl>',
				'</div>'
				// ,
				// '<div class="x-clear"></div>'
			)
		}]
	});
	
	CE.add(p);
	CE.doLayout();
});
</script>