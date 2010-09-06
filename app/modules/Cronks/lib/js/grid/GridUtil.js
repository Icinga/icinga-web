
Ext.ns('Cronk.grid.GridUtil');

Cronk.grid.GridUtil = (function() {

	var pub = {};
	
	Ext.apply(pub, {
		initCommentEventHandler : function(grid, c) {
			
			if (Ext.isEmpty(c.column_name)) {
				throw("initCommentEventHandler: Need arguments->column_name to determine fields");
			}
			
			grid.on('cellclick', function(lGrid, rowIndex, columnIndex, e) {
				var column_name = lGrid.getColumnModel().getDataIndex(columnIndex);
				if (column_name == c.column_name) {
					var record = grid.getStore().getAt(rowIndex); 
					var node = AppKit.util.parseDOMfromString(record.get(c.column_name));
					Icinga.util.SimpleDataProvider.createToolTip({
						target: node.getAttribute('id'),
						title: Cronk.grid.ColumnRendererUtil.applyXTemplate(lGrid, rowIndex, node.getAttribute('title')),
						width: c.width || 400,
						filter: [{key: 'object_id', value: node.firstChild.data}],
						srcId: c.sourceId || 'comments'
					});
					
				}
			}, this);
		}
	});
	
	return pub;

})();
