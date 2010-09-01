Ext.ns('Cronk.grid');

// These are the javascript methods available within
// the namespace
Cronk.grid.IcingaColumnRenderer = {
	
	subGrid : function(cfg) {
		return function(grid, rowIndex, colIndex, e) {
			var fieldName = grid.getColumnModel().getDataIndex(colIndex);
			if (fieldName == cfg.field) {
				
				var record = grid.getStore().getAt(rowIndex);
				var id = (cfg.idPrefix || 'empty') + 'subGridComponent';
				
				var cronk = {
					parentid: id,
					title: (cfg.titlePrefix || '') + " " + record.data[ cfg.labelField ],
					crname: 'gridProc',
					closable: true,
					params: {template: cfg.targetTemplate}
				};
				
				var filter = {};
				
				if (cfg.filterMap) {
					Ext.iterate(cfg.filterMap, function(k, v) {
						filter["f[" + v + "-value]"] =  record.data[ k ];
						filter["f[" + v + "-operator]"] = 50;
					});
				}
				else {
					filter["f[" + cfg.targetField + "-value]"] = record.data[ cfg.sourceField ];
					filter["f[" + cfg.targetField + "-operator]"] = 50;
				}
				
				Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
			}
		}
	},
	
	ajaxClick : function(cfg) {
		return function(grid, rowIndex, colIndex, e) {
			var fieldName = grid.getColumnModel().getDataIndex(colIndex);
			if (fieldName == cfg.field) {

				cfg.processedFilterData = [];

				Ext.iterate(
					cfg.filter,
					function (key, value) {
						this.push({key: key, value: grid.getStore().getAt(rowIndex).data[value]});
					},
					cfg.processedFilterData
				);
				
				Icinga.util.SimpleDataProvider.createToolTip({
					title: cfg.title,
					target: e.getTarget(),
					srcId: cfg.src_id,
					width: 400,
					delay: 15000,
					filter: cfg.processedFilterData
				});

			}
		}

	},

	hyperLink : function(cfg) {

		if (!'url' in cfg) {
			throw('url XTemplate configuration needed! (parameter name="url")');
		}

		return function(grid, rowIndex, colIndex, e) {
			
			var fieldName = grid.getColumnModel().getDataIndex(colIndex);

			if (fieldName == cfg.field) {
				var data = grid.getStore().getAt(rowIndex).data;
				var tpl = new Ext.XTemplate(cfg.url);
				var url = tpl.apply(data);
				var windowName = fieldName;

				if (Ext.isEmpty(cfg.newWindow) || cfg.newWindow == false) {
					windowName = '_self';
				}

				window.open(url, windowName);
			}
		}
	},

	iFrameCronk: function(cfg) {
		
		if (!'url' in cfg) {
			throw('url XTemplate configuration needed! (parameter name="url")');
		}
		return function(grid, rowIndex, colIndex, e) {
			var data = grid.getStore().getAt(rowIndex).data;
			var urlTpl = new Ext.XTemplate(cfg.url);
			var url = urlTpl.apply(data);
			var titleTpl = new Ext.XTemplate(cfg.title);
			var title = titleTpl.apply(data);
			var tabPanel = Ext.getCmp("cronk-tabs");
			AppKit.log(url);
			var cmp = tabPanel.add({
				'xtype': 'cronk',
				'title': title,
				'crname': 'genericIFrame',
				'params': {
					url:  url
				},
				'closable':true
			});
			tabPanel.doLayout();
		
			if (!Ext.isEmpty(cfg.activateOnClick) && cfg.activateOnClick) {
				tabPanel.setActiveTab(cmp);
			}
				
		}
	}
};
