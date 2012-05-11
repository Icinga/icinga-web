// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

Ext.ns("AppKit.util");

AppKit.util.AppKitNavBar = Ext.extend(Ext.Container,{ 
    layout:         null, 
    menuData:       {},
    preferenceURL:  AppKit.util.Config.get('path') + '/modules/my/preferences',
    logoutURL:      null,
    username:       null,
    hasAuth:        false,
    navBar:         null,
    iconField:      null,


    defaultCfg: {
        layout: 'column',
        id: 'menu',
        border: false,
        defaults: {style: {borderLeft: '1px #d0d0d0 solid'}, border: false, height: 40}
    },

    // default config for the menubar field
    tbarCfg: {
        id: 'menu-navigation',
        defaults: {border: false, style: 'margin:2px'},
        style: 'border: none',  
        height: 35,
        items: {},
        columnWidth: 1
    },

    // default config for the icon field
    iconFieldCfg: {
        id: 'menu-logo',
        width: 60,
        height: 30,
        border: false,
        cls: 'icinga-link',
        items: {
            width: 61,
            border: false,
            autoEl: 'div',
            frame: false,
            cls: 'menu-logo-icon'   
        }
    },

    constructor: function(cfg) {
        if(Ext.getCmp(this.defaultCfg.id))
            throw("Menubar is already loaded");   
        Ext.apply(this,cfg); 

        this.buildNavBar();
        this.buildIconField();
        Ext.apply(cfg,this.defaultCfg);
        cfg.items = [
                     this.navBar,
                     this.iconField
                     ];

        Ext.Container.prototype.constructor.call(this,cfg); 
    }, 


    buildNavBar:function() {
        var cfg = {};
        cfg = Ext.apply(cfg,this.tbarCfg); 
        this.initMenuItems(cfg);
        this.navBar = new Ext.Toolbar(cfg);
    },

    buildIconField : function() {
        this.iconField = new Ext.Container(this.iconFieldCfg);
        // Make the icon funky when loading
        Ext.Ajax.on("beforerequest",function() {
            try {
                var icon = Ext.DomQuery.selectNode('.menu-logo-icon');
                if(!icon)
                    return true;
                Ext.get(icon).setStyle('background-image','url('+AppKit.c.path+'/images/ajax/icinga-throbber.gif)');
            } catch(e) {
                // ignore any errors
            }
        });
        Ext.Ajax.on("requestcomplete",function() {
            try {
                var icon = Ext.DomQuery.selectNode('.menu-logo-icon');
                if(!icon)
                    return true;
                Ext.get(icon).setStyle('background-image','url('+AppKit.c.path+'/images/icinga/idot-small.png)');
            } catch(e) {
                // ignore any errors
            }
        });


        this.iconField.on('render', function(c) {
            c.getEl().on('click', function() {
                AppKit.changeLocation('http://www.icinga.org');
            });
        });
    },

    initMenuItems : function(cfg) {
        cfg.items = [];
        this.addMenuFields(cfg.items,this.menuData); 
        cfg.items.push({xtype : 'tbfill'});
        this.addClock(cfg.items);

        if (AppKit.search.SearchHandler.isReady() === true) {
            this.addSearchBox(cfg.items);
        }

        this.addUserFields(cfg.items);
    },

    addClock : function(itemsCfg) {
        var item = new AppKit.util.Servertime();
        itemsCfg.push({xtype: 'container',items:item});
    },

    addSearchBox : function(itemsCfg) {
        var item = new AppKit.search.Searchbox();
        AppKit.search.SearchHandler.setSearchbox(item);
        itemsCfg.push(item);
    },

    addMenuFields : function(itemsCfg,menuData) {

        for(var i=0;i<menuData.length;i++) {
            var menuPoint = menuData[i];
            var p = {
                    text: _(menuPoint.caption),
                    iconCls: menuPoint.icon || null,
                    id: menuPoint.id || Ext.id()          
            };         
            if(menuPoint.target) {
                p.handler = this.createHandlerForTarget(menuPoint.target);

                // To allow native browser actions e.g. 'open in new tab', ...
                if (menuPoint.target.target == 'new' && "url" in menuPoint.target) {
                    p.href = menuPoint.target.url;
                }
            }
            if(menuPoint.items) {
                p.menu = [];
                this.addMenuFields(p.menu,menuPoint.items);
            }
            itemsCfg.push(p); 
        }

    },

    createHandlerForTarget: function(target) {
        switch(target.target) {
        case 'new':
            return Ext.createDelegate(AppKit.changeLocation,window,[target.url]); 
        case 'container':
            return function() {
            var el = Ext.get(target.id);
            if(!el) {
                AppKit.log("Error: id "+target.id+" not found");

            } else {
                var updater = el.getUpdater();
                updater.update({
                    url:target.url,
                    params: target.params || null
                });
            }
        };

        case 'window':
            target.bodyStyle = target.style || "background-color: #ffffff";
            return Ext.createDelegate(AppKit.util.contentWindow, this, [{
                url: target.url
            },  target]);  
        }
    },

    addUserFields : function(itemsCfg) { 

        var userField = {
                iconCls: this.hasAuth ? 'icinga-icon-user' : 'icinga-icon-user-delete',
                        text: this.username
        };
        if(this.hasAuth) {

            userField.menu = {};
            userField.menu.items = {
                    xtype: 'buttongroup',
                    columns: 2,
                    autoWidth: true,
                    defaults: {
                        scale: 'large',
                        iconAlign: 'left',
                        width: '100%'

                    },
                    items: [{
                        tooltip: _('Preferences'),
                        iconCls: 'icinga-icon-user-edit',
                        text: _('Preferences'),
                        handler: function() { 
                            AppKit.util.doPreferences(this.preferenceURL);
                        },
                        scope: this
                    }, {
                        tooltip: _('Logout'),
                        iconCls: 'icinga-icon-user-go',
                        width: 'auto',
                        handler: function() {
                            AppKit.util.doLogout(this.logoutURL);
                        },
                        scope:this
                    }]
            }; 
        }
        itemsCfg.push(userField);
    }
});


