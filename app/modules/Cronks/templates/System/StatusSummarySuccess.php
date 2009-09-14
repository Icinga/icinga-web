<?php
	/**
	* @author Christian Doebler <christian.doebler@netways.de>
	*/
	$htmlid = $rd->getParameter('htmlid');
?>

<script type="text/javascript">

var CronkDisplayStateSummary = {

	cmp : Ext.getCmp("<?php echo $htmlid; ?>"),
	url : "<?php echo $ro->gen('icinga.cronks.statusSummary.json'); ?>",

	panelDefs : {
		host : {
			itemId : "panel-hosts",
			title : false,
		},
		service : {
			itemId : "panel-services",
			title : false,
		},
		chart : {
			itemId : "panel-chart",
			title : "charts",
			width: 600,
			height: 400
		}
	},

	store : false,
	tpl : false,
	view : false,
	panel : false,

	storeCollection : new Array(),

	init : function () {
		this.createPanel();
		this.showGrid("host");
		//this.reset();
		this.showGrid("service");
		this.showCharts();
		Ext.getCmp("view-container").doLayout();
	},

	reset : function () {
		this.store = false;
		this.tpl = false;
		this.view = false;
	},

	createPanel : function () {
		this.panel = new Ext.Panel({
			layout: "column",
			defaults: {
				border: false,
				cls: "no-background"
			},
			items: [
				{
					itemId: this.panelDefs.host.itemId,
					title: ((this.panelDefs.host.title !== false) ? this.panelDefs.host.title : false)
				},{
					itemId: this.panelDefs.service.itemId,
					title: ((this.panelDefs.service.title !== false) ? this.panelDefs.service.title : false)
				},{
					itemId: this.panelDefs.chart.itemId,
					title: ((this.panelDefs.chart.title !== false) ? this.panelDefs.chart.title : false)
				}
			]
		});
		this.cmp.add(this.panel);
	},

	showGrid : function (type) {

		// Our store to retrieve the cronks
		this.store = new Ext.data.JsonStore({
			url: this.url,
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
			id: "huha" + type,
			title: "test",
			store: this.store,
			tpl: this.tpl,
			itemSelector:"div.test-l",
			emptyText: "No data"
		});

		//this.cmp.add(this.view);
		this.panel.getComponent(this.panelDefs[type].itemId).add(this.view);

		// process data source for graphing
		var storeTmp = new Ext.data.JsonStore({
			fields: [0, 1, 2, 3],
			data: new Array()
		});
		
		// save data source
		this.storeCollection.push(this.store);

	},

	showCharts : function () {

		var numStores = this.storeCollection.length;

		for (var x = 0; x < numStores; x++) {

			var chart = new Ext.chart.StackedBarChart({
				width: 200,
				height: 100,
				store: this.storeCollection[x],
				yField: "type",
				xAxis: new Ext.chart.NumericAxis({
					stackingEnabled: true,
				}),
				series: [{
					xField: "count",
					displayName: "Count"
				}]
			});

			this.panel.getComponent(this.panelDefs.chart.itemId).add(chart);

		}

	}

}

CronkDisplayStateSummary.init();

</script>
