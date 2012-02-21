Ext.namespace('Ext.ux.grid');

Ext.ux.grid.EllipsisColumn = Ext.extend(Ext.grid.Column, {
	selectableClass: 'x-icinga-grid-cell-selectable',
	
	constructor: function(c) {
		Ext.ux.grid.EllipsisColumn.superclass.constructor.call(this, c);
		var vname = '{' + this.dataIndex + '}';
		this.tpl = new Ext.XTemplate('<span ext:qtip="' + vname + '">' + vname + '</span>');
		this.renderer = (function(value, p, r) {
			p.css += ' ' + this.selectableClass;
			return this.tpl.apply(r.data);
		}).createDelegate(this);
	}
});

Ext.grid.Column.types.ellipsiscolumn = Ext.ux.grid.EllipsisColumn;