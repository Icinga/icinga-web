/*global Ext: false, Icinga: false, _: false */
(function() {
 //   "use strict";

    Ext.ns("Icinga.Cronks.Tackle.ExternalReferences").UrlList = Ext.extend(Ext.list.ListView,{
        layout : 'fit',
        flex : 1,

        events: {
            "urlselected" : true
        },

        constructor: function(cfg) {
            cfg = cfg || {};
            Ext.apply(cfg,{
                columns: [{
                    header: 'Type',
                    width: .2,
                    dataIndex: 'type'
                },{
                    header: 'URL',
                    width: .8,
                    dataIndex: 'url'
                }]
            });
            cfg.title = _("Url list");
            cfg.singleSelect = true;
            Ext.list.ListView.prototype.constructor.call(this,cfg);
        },
        initComponent: function() {
            Ext.list.ListView.prototype.initComponent.apply(this,arguments);
            this.store = new Ext.data.JsonStore({
                fields: ['type','url']
            });
          
        },
        
        getStore: function() {
            return this.store;
        },

        viewConfig : {
            forceFit : true
        }

    });
})();