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
		}
	},

	store : false,
	tpl : false,
	view : false,
	panel : false,

	init : function () {
		this.createPanel();
		this.showGrid("host");
		//this.reset();
		this.showGrid("service");
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

	}

}

CronkDisplayStateSummary.init();

</script>
