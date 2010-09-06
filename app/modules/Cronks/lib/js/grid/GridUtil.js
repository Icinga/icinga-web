
Ext.ns('Cronk.grid.GridUtil');

Cronk.grid.GridUtil = (function() {

	var pub = {};
	
	Ext.apply(pub, {
		initCommentEventHandler : function(grid, c) {
			
			if (Ext.isEmpty(c.type)) {
				throw("initCommentEventHandler: arguments->type is needed (type=<host|service>)");
			}
			
			if (!Ext.isEmpty(c.column_name)) {
				throw("initCommentEventHandler: Need arguments->column to determine fields");
			}
			
			grid.on('cellclick', function(lGrid, rowIndex, columnIndex, e) {
				var column_name = lGrid.getColumnModel().getDataIndex(columnIndex);
				if (column_name == c.column_name) {
					var record = grid.getStore().getAt(rowIndex); 
					var node = AppKit.util.parseDOMfromString(record.get(column_name));
					
					Icinga.util.SimpleDataProvider.createToolTip({
						target: node.getAttribute('id'),
						title: 
					});
					
				}
			});
		}
	});
	
	return pub;

})();
