<?php
	/**
	* @author Christian Doebler <christian.doebler@netways.de>
	*/
	$parentid = $rd->getParameter('parentid');
?>
<script type="text/javascript">
	
var dummyCronkDisplayStateSummary = function () {

	var CronkDisplayStateSummary = {
	
		cmp : Ext.getCmp("<?php echo $parentid; ?>"),
		url : "<?php echo $ro->gen('icinga.cronks.statusSummary.json'); ?>?dtype=",
	
		panelDefs : {
			host : {
				itemId : AppKit.Ext.genRandomId('cronk'),
				title : false,
			},
			service : {
				itemId : AppKit.Ext.genRandomId('cronk'),
				title : false,
			},
			chart : {
				itemId : AppKit.Ext.genRandomId('cronk'),
				title : false
			}
		},
	
		store : false,
		tpl : false,
		view : false,
		panel : false,
		outputType : false,
	
		loaded : false,
		storeCollection : new Array(),
	
		init : function (outputType) {
			this.outputType = outputType;
			this.createPanel();
			Ext.getCmp("view-container").doLayout();
			Ext.TaskMgr.start({
				run: this.refresh,
				interval: 300 * 1000
			});
		},
	
		refresh : function () {
			if (CronkDisplayStateSummary.loaded !== false) {
				var numStores = CronkDisplayStateSummary.storeCollection.length;
				for (var x = 0; x < numStores; x++) {
					CronkDisplayStateSummary.storeCollection[x].reload();
				}
			} else {
				switch (CronkDisplayStateSummary.outputType) {
					case "text":
						CronkDisplayStateSummary.showGrid("host");
						CronkDisplayStateSummary.showGrid("service");
						CronkDisplayStateSummary.loaded = true;
						break;
					case "chart":
						CronkDisplayStateSummary.showChart("host");
						CronkDisplayStateSummary.showChart("service");
						CronkDisplayStateSummary.loaded = true;
						break;
				}
			}
		},
	
		createPanel : function () {
			switch (this.outputType) {
				case "text":
					this.panel = new Ext.Panel({
						layout: "column",
						defaults: {
							border: false,
							cls: "no-background"
						},
						items: [
							{
								itemId: this.panelDefs.host.itemId,
								title: ((this.panelDefs.host.title !== false) ? this.panelDefs.host.title : false),
								style: {
									marginRight: "10px"
								}
							},{
								itemId: this.panelDefs.service.itemId,
								title: ((this.panelDefs.service.title !== false) ? this.panelDefs.service.title : false)
							}
						]
					});
					break;
				case "chart":
					this.panel = new Ext.Panel({
						//layout: "column",
						defaults: {
							border: false,
							cls: "no-background"
						},
						items: [
							{
								itemId: this.panelDefs.chart.itemId,
								title: ((this.panelDefs.chart.title !== false) ? this.panelDefs.chart.title : false)
							}
						]
					});
					break;
			}
			this.cmp.add(this.panel);
		},
	
		showGrid : function (type) {
	
			// Our store to retrieve the cronks
			this.store = new Ext.data.JsonStore({
				url: this.url + type,
				root: "status_data.data",
				autoLoad: false,
				fields: ["state_id", "state_name", "type", "count"],
				listeners: {
					load: function(s) {
						s.filter("type", type);
					}
				}
			});
	
			// Load the data
			this.store.load();
	
			// Template to display the cronks
			this.tpl = new Ext.XTemplate(
				"<tpl for=\".\">",
					"<div class=\"test-l\" id=\"{state_id}\">",
						"<span class=\"x-editable\">{count}</span>&nbsp;",
						"<span class=\"x-editable\">{state_name}</span>",
					"</div>",
				"</tpl>",
				"<div class=\"x-clear\"></div>"
			);
	
			// The dataview container
			this.view = new Ext.DataView({
				id: AppKit.Ext.genRandomId('cronk'),
				title: "test",
				store: this.store,
				tpl: this.tpl,
				itemSelector:"div.test-l",
				emptyText: "No data"
			});
	
			this.panel.getComponent(this.panelDefs[type].itemId).add(this.view);
	
			this.storeCollection.push(this.store);
	
		},
	
		showChart : function (type) {
	
			this.store = new Ext.data.JsonStore({
				url: this.url + type + "chart",
				root: "status_data.data",
				autoLoad: false,
				fields: ["type", "OK", "UNKNOWN", "DOWN", "WARNING", "CRITICAL"]
			});
			this.store.load();
	
			var chart = new Ext.chart.StackedBarChart({
				width: 150,
				height: 80,
				store: this.store,
				yField: "type",
				xAxis: new Ext.chart.NumericAxis({
					stackingEnabled: true
				}),
				chartStyle: {
					xAxis: {
						majorGridLines: {size: 0},
						showLabels: false,
						margin: 0,
						padding: 0
					},
					yAxis: {
						showLabels: false,
						margin: 0,
						padding: 0
					},
					margin: 0,
					padding: 0
				},
				series: [
					{
						xField: "OK",
						displayName: "OK",
						style: {
							color: 0x00ff00
						}
					},{
						xField: "UNKNOWN",
						displayName: "UNKNOWN",
						style: {
							color: 0xff8040
						}
					},{
						xField: "DOWN",
						displayName: "DOWN",
						style: {
							color: 0xff0000
						}
					},{
						xField: "WARNING",
						displayName: "WARNING",
						style: {
							color: 0xffff00
						}
					},{
						xField: "CRITICAL",
						displayName: "CRITICAL",
						style: {
							color: 0xff0000
						}
					}
				]
			});
	
			this.panel.getComponent(this.panelDefs.chart.itemId).add(chart);
	
			this.storeCollection.push(this.store);
	
		}
	
	}
	
	CronkDisplayStateSummary.init("<?php echo $rd->getParameter('otype'); ?>");

}();
	
</script>