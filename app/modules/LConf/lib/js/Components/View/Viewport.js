Ext.ns("LConf.View").Viewport = function(cfg) {
    
    cfg.eventDispatcher = new LConf.EventDispatcher();
    cfg.filterState = new LConf.Filter.FilterState(cfg);

    var mainContainer =  new Ext.Panel({
		layout:'border',
		id: 'view-container',
		defaults: {
			split:true,
			collapsible: true
		},
		border: false,
		items: [{
			title: 'DIT',
			region: 'west',
			id: 'west-frame-lconf',
			layout: 'fit',
			margins:'5 0 0 0',
			cls: false,
			width:400,
			minSize:200,
			maxSize:500,
            items: new LConf.View.DITPanel(cfg)
		}, {
			region:'center',
			collapsible:false,
			title: "Properties",
			layout: 'fit',
			id:'center-frame',
			margins: '5 0 0 0',
            items: new LConf.View.PropertyManager(cfg)
/*
                url: '<?php echo $ro->gen("modules.lconf.data.modifyproperty");?>',
                api: {
                    read :'<?php echo $ro->gen("modules.lconf.data.propertyprovider");?>'
                }*/
           
		},{
			title: 'Actions',
			region: 'east',
			id: 'east-frame',
			layout: 'accordion',
			animate:true,
			margins:'5 0 0 0',
			cls: false,
			width:200,
			minSize:100,
			maxSize:200,
            items:[
                new LConf.View.ConnectionList(cfg),
                new LConf.View.FilterPanel(cfg)
            ]
		}]
	});
    
    if(cfg.connId) {
        cfg.eventDispatcher.addCustomListener("connectionsLoaded",function(store,conn) {
            conn.startConnect(store.indexOfId(cfg.connId));
            if(cfg.dn)
                cfg.eventDispatcher.addCustomListener("TreeReady",function(tree) {
                    tree.searchDN(cfg.dn);
                });
        },this,{single:true});
    }
	cfg.parentCmp.add(mainContainer);
	cfg.parentCmp.doLayout();
}
