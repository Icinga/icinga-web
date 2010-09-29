<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {
	var CE = this;
	
	var ds = new Ext.data.JsonStore({
		url: '<?php echo $ro->gen('cronks.monitorPerformance.json') ?>',
		storeId: 'overall-status-store'
	});

	ds.load();
	
	var interval = <?php echo $us->getPrefVal('org.icinga.grid.refreshTime', AgaviConfig::get('modules.cronks.grid.refreshTime', 120)); ?>;
	
	var monitorPerformanceRefreshTask = {
		run: function() { ds.reload(); },
		interval: (1000*interval)
	}

	AppKit.getTr().start(monitorPerformanceRefreshTask);
	
	var monitorPerformancePanel = new Ext.DataView({
		store: ds,
		tpl: new Ext.XTemplate(
			'<tpl for=".">',
			
			'<div class="float-container clearfix icinga-monitor-performance">',
			
			'<div class="icinga-monitor-performance-container-50">',
			
				'<div class="clearfix icinga-monitor-performance-container">',
					'<div title="' + _('Hosts (active/passive)') + '" class="key icinga-icon-host"></div>',
					'<div class="value">{NUM_ACTIVE_HOST_CHECKS} / {NUM_PASSIVE_HOST_CHECKS}</div>',
				'</div>',
			
				'<div class="clearfix icinga-monitor-performance-container">',
					'<div title="' + _('Host execution time (min/avg/max)') + '" class="key icinga-icon-execution-time"></div>',
					'<div class="value">{HOST_EXECUTION_TIME_MIN} / {HOST_EXECUTION_TIME_AVG} / {HOST_EXECUTION_TIME_MAX}</div>',
				'</div>',
				
				'<div class="clearfix icinga-monitor-performance-container">',
					'<div title="' + _('Host latency (min/avg/max)') + '" class="key icinga-icon-latency"></div>',
					'<div class="value">{HOST_LATENCY_MIN} / {HOST_LATENCY_AVG} / {HOST_LATENCY_MAX}</div>',
				'</div>',
			
			'</div>',
			
			'<div class="icinga-monitor-performance-container-50">',
			
				'<div class="clearfix icinga-monitor-performance-container">',
					'<div title="' + _('Services (active/passive)') + '" class="key icinga-icon-service"></div>',
					'<div class="value">{NUM_ACTIVE_SERVICE_CHECKS} / {NUM_PASSIVE_SERVICE_CHECKS}</div>',
				'</div>',
				
				'<div class="clearfix icinga-monitor-performance-container">',
					'<div title="' + _('Service execution (min/avg/max)') + '" class="key icinga-icon-execution-time"></div>',
					'<div class="value">{SERVICE_EXECUTION_TIME_MIN} / {SERVICE_EXECUTION_TIME_AVG} / {SERVICE_EXECUTION_TIME_MAX}</div>',
				'</div>',
				
				'<div class="clearfix icinga-monitor-performance-container">',
					'<div title="' + _('Service latency (min/avg/max)') + '" class="key icinga-icon-latency"></div>',
					'<div class="value">{SERVICE_LATENCY_MIN} / {SERVICE_LATENCY_AVG} / {SERVICE_LATENCY_MAX}</div>',
				'</div>',
				
			'</div>',
			
			'</div>',
			
			'</tpl>'
		)
	});
	
	CE.add(monitorPerformancePanel);
	CE.doLayout();	
});
</script>
