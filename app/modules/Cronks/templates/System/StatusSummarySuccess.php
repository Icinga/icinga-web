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

		objectType : false,
	
		panelDefs : {
			host : {
				itemId : AppKit.Ext.genRandomId("cronk"),
				title : false
			},
			service : {
				itemId : AppKit.Ext.genRandomId("cronk"),
				title : false
			},
			chart : {
				itemId : AppKit.Ext.genRandomId("cronk"),
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
				fields: ["state_id", "state_name", "type", "type_name", "count"],
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
					"<div class=\"status-summary-row\" id=\"{state_id}\">",
						"<span class=\"x-editable\">{count}</span>&nbsp;",
						"<span class=\"x-editable\">{state_name}</span>",
					"</div>",
				"</tpl>",
				"<div class=\"x-clear\"></div>"
			);
	
			// The dataview container
			this.view = new Ext.DataView({
				id: AppKit.Ext.genRandomId("cronk"),
				title: false,
				store: this.store,
				tpl: this.tpl,
				itemSelector:"div.status-summary-row",
				emptyText: "No data",
				trackOver: true,
				singleSelect: true,

				listeners: {
					click: function(view, index, node, e) {
						var record = view.getStore().getAt(index);
						var type = record.data.type;
						var params = {};
						var filter = {};

						var id = (type || "empty") + "searchResultComponent";

						switch (type) {
							case "host":
								switch (record.data.state_id) {
									case 0:
									case 1:
									case 2:
										filter["f[host_status-value]"] = record.data.state_id;
										filter["f[host_status-operator]"] = 50;
										break;
									case 10:
										filter["f[host_status-value]"] = 0;
										filter["f[host_status-operator]"] = 71;
										break;
								}
								params["template"] = "icinga-host-template";
								break;

							case "service":
								switch (record.data.state_id) {
									case 0:
									case 1:
									case 2:
									case 3:
										filter["f[service_status-value]"] = record.data.state_id;
										filter["f[service_status-operator]"] = 50;
										break;
									case 10:
										filter["f[service_status-value]"] = 0;
										filter["f[service_status-operator]"] = 71;
										break;
								}
								params["template"] = "icinga-service-template";
								break;

							default:
								Ext.Msg.alert("Search", "This type is not ready implemented yet!");
								return;
								break;
						}

						var cronk = {
							parentid: id,
							title: record.data.type_name + " - " + record.data.state_name,
							crname: "gridProc",
							closable: true,
							params: params
						};

						AppKit.Ext.util.InterGridUtil.gridFilterLink(cronk, filter);

						return true;
					}
				}
			});
	
			this.panel.getComponent(this.panelDefs[type].itemId).add(this.view);
	
			this.storeCollection.push(this.store);
	
		},

		showChart : function (type) {
			this.objectType = type;

			var ajax = Ext.Ajax.request({
				url : this.url + type + "chart",
				params : false,
				method : "GET",
				success : this.showChartAjaxSuccess,
				failure : this.showChartAjaxFail,
				callback : this.showChartAjaxDefault,
				scope: this,
				timeout : 50000,
				disableCaching : true
			});
		},

		showChartAjaxSuccess : function (response, o) {
			var json = Ext.util.JSON.decode(response.responseText);
			if (json.status_data.data.length) {
				this.drawChart(json.status_data.data);
			}
		},

		showChartAjaxFail : function (response, o) {
			
		},

		showChartAjaxDefault : function (options, success, request) {
			
		},

		drawChart : function (data) {
			var numObjects = 0;
			var graphElements = [];
			var dataType = false;

			for (var key in data) {
				for (var statusDataKey in data[key]) {
					if (statusDataKey != 0 && statusDataKey != 1 && statusDataKey != 2 && statusDataKey != 3 && statusDataKey != 4) {
						break;
					}
					var statusData = data[key][statusDataKey];
					if (dataType == false) {
						dataType = statusData.type;
					}
					if (statusData.state_id == 20) {
						numObjects = statusData.count;
						continue;
					}
					if (statusData.count) {
						var currentElement = {
							status_id: statusData.state_id,
							status_name: statusData.state_name,
							status_count: statusData.count
						};
						graphElements.push(currentElement);
					}
				}
			}

			var containerWidth = (this.panel.getComponent(this.panelDefs.chart.itemId).body.dom.clientWidth / 2) - 60;
			var numElements = graphElements.length;
			var containerItems = [];

			for (var x = 0; x < numElements; x++) {
				var currentItem = {
					cls: dataType + "-" + graphElements[x].status_id,
					style: "width:" + parseInt((graphElements[x].status_count / numObjects) * containerWidth) + "px;"
				};
				containerItems.push(currentItem);
			}

			var container = new Ext.Container({
				cls: "status-summary-" + dataType,
				autoEl: 'div', 
				layout: 'column',
				defaults: {
					xtype: 'container',
					autoEl: 'div',
					layout: 'auto',
					style: {
						border: "none"
					}
				},
				items : containerItems
			});

			var parentCmp = this.panel.getComponent(this.panelDefs.chart.itemId);
			parentCmp.add(container);
			parentCmp.doLayout();
		}

	}
	
	CronkDisplayStateSummary.init("<?php echo $rd->getParameter('otype'); ?>");

}();
	
</script>