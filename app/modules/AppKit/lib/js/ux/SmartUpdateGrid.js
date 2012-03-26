/**
 * Grid implementation that doesn't refresh the whole page, but rather
 * updates rows that changed, removes rows that are not visible anymore and
 * adds new rows.
 *
 */

Ext.namespace('Ext.ux.grid').SmartUpdateGridView = Ext.extend(Ext.grid.GridView,{
    initialRender: false,
    
    onClear:function() {
        AppKit.log("Clear called");
        Ext.grid.GridView.prototype.onClear.apply(this,arguments);
    },

    onDataChange: function() {
        if(this.initialRender == true) {
            AppKit.log("updating rows");
            this.ds.each(function(record) {
                this.refreshRow(record);
            },this)
        } else {
            this.initialRender = true;
            Ext.grid.GridView.prototype.onDataChange.apply(this,arguments);
            AppKit.log("refreshing all rows");
        }
    }
});

Ext.namespace('Ext.ux.grid').SmartUpdateGrid = Ext.extend(Ext.grid.GridPanel,{


    getView : function() {
        if (!this.view) {
            this.view = new Ext.ux.grid.SmartUpdateGridView(this.viewConfig);
        }

        return this.view;
    }
});
