Ext.ns('Cronk.grid.ColumnRenderer');
(function() {

	var ldapColumnSelector = new Ext.util.DelayedTask(function(cfg) {
		var objsToCheck = (Ext.DomQuery.jsSelect("div.lconf_cronk_sel"));
		var idsToCheck = [];
		cfg.elems = {};
		Ext.each(objsToCheck,function(obj) {
			var elem = Ext.get(obj);
			var obj_id = elem.getAttribute('lconf_val');
			idsToCheck.push(obj_id);
			if(!cfg.elems[obj_id])
				cfg.elems[obj_id] = elem;
			else if(!Ext.isArray(cfg.elems[obj_id]))
				cfg.elems[obj_id] = [cfg.elems[elem.getAttribute('lconf_val')],elem];
			else 
				cfg.elems[obj_id].push(elem);
		},this);
		requestElementsWithDN(idsToCheck,cfg,drawLinks,this);
	});
		
	var requestElementsWithDN = function(ids,cfg,callback,scope) {
		if(!Ext.isArray(ids))
			return false;
		Ext.Ajax.request({
			url: window.location.protocol+"//"+window.window.location.host+AppKit.c.path+"/"+cfg.url,
			params: {
				ids: Ext.encode(ids),
				target: cfg.target,
				target_field: cfg.target_field
			},
			failure: function(resp) {
				var err = Ext.decode(resp.responseText);
				AppKit.notifyMessage("LDAP Error",_("Couldn't fetch ldap information")+",<br/> "+err.msg);
				
			},
			success: function(resp) {
				var data = Ext.decode(resp.responseText);
				if(!data)
					AppKit.notifyMessage("LDAP Error",_("Invalid server response"));
				else
					callback.call(scope,Ext.apply(cfg,{dn:data}),cfg);
			}
		});
	}
	
	var drawLinks = function(data,cfg) {
		for(var id in data.elems) {
			// Check if we have multiple results
			if(!Ext.isArray(data.elems[id])) {
				handleCSSSelector(data,data.elems[id],id,cfg);
			} else 
				Ext.each(data.elems[id],function(elem) {
					handleCSSSelector(data,elem,id,cfg)
				});
		}	
	}
	
	var handleCSSSelector = function(data,elem,id,cfg) {

		if(!data.dn[id]) {
			elem.replaceClass("unfinished","notAvailable");
			return true;
		}
		var dnInfo = data.dn[id];
		elem.replaceClass("unfinished","available");
        registerQTip(elem);
		elem.on("click",function(e) {showDNMenu(e,dnInfo,cfg)},this);
	}

    var registerQTip = function(elem) {
        var qtipAttr = elem.getAttribute("qtip","ext");
       
        if(qtipAttr !== '') {
           new Ext.ToolTip({
                target: Ext.get(elem.findParentNode('td')),
                html: _(qtipAttr)
            }).doLayout();
        }
    }
	
	var showDNMenu = function(e,dnInfo,cfg) {
		var menuItems = [];
	
		Ext.each(dnInfo.Connections,function(connection) {
			menuItems.push({
				text: _('Use ')+connection.name,
				handler: function() {

                    var url = AppKit.c.path+"/"+cfg.ldapRoute;
                    
					window.location.href = url+"/"+connection.id+"/"+dnInfo.DN;				
				}
			});
		});
		new Ext.menu.Menu({
			items: menuItems
		}).showAt(e.getXY());
	}
	
	Cronk.grid.ColumnRenderer.ldapColumn = 	function(cfg) {
		
		return function(value, metaData, record, rowIndex, colIndex, store) {
			ldapColumnSelector.delay(200,null,null,[cfg]);
            

            var flat_attr = "";
            for(var attr in cfg.attr) {
                flat_attr = attr+'="'+cfg.attr[attr]+'"';
            }
			return '<div class="lconf_cronk_sel unfinished" '+flat_attr+' lconf_val="'+value+'"><div style="width:25px;height:25px;display:block;" ></div></div>';
		}
	}
		

})()