// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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
/*jshint browser:true, curly:false */
/*global Ext: false, Icinga: false, _: false*/
Ext.ns("Icinga.Cronks.util").FilterEditorWindow = function(grid, filters, btn) {
    "use strict";

    /**
     * CSS class name for a button to mark filter active
     * @type {string}
     */
    var filterActiveCls = 'activeFilter';

    /**
     * Mark the filter as active
     *
     * Sets the class on the button
     *
     * @param {Boolean} isActive
     */
    var changeActiveState = function(isActive) {
        isActive = Boolean(isActive);
        if (isActive === true) {
            btn.addClass(filterActiveCls);
        } else {
            btn.removeClass(filterActiveCls);
        }
    };

    this.updateFromJsonString = function(json) {

        if(Ext.isString(json))
            this.filter = Ext.decode(json);
        else
            this.filter = json;
        if(this.state)
            this.state.update(this.filter);

        changeActiveState(Ext.isEmpty(json) === false);

        grid.getStore().load();
    };

    this.show = function() {
        var tree = new Icinga.Cronks.util.FilterEditor({
            autoDestroy: false,
            grid: grid,
            filterCfg:filters
        });

        tree.on('filterchanged', function(fo) {
            if (!fo) {
                changeActiveState(false);
            } else {
                changeActiveState(true);
            }
        });

        this.state = new Icinga.Cronks.util.FilterState({
            autoDestroy: false,
            grid: grid,
            tree: tree
        });
        var filterPanel = this.getFilterPanel(tree);
        var cronkPanel = Ext.getCmp('west-frame');
        registerEvents(filterPanel,cronkPanel);
        
        cronkPanel.add(filterPanel);
        cronkPanel.getLayout().setActiveItem(1);
    };
    
    
    this.getFilterPanel = function(tree) {
        if(this.filter)
            this.state.update(this.filter);
        return new Ext.Panel({
            tbar: [{
                xtype: 'button',
                text: _('Back to cronks'),
                iconCls: 'icinga-icon-arrow-left',
                handler: function() {
                    Ext.getCmp('west-frame').resetCronkView();
                }
            }],
            layout:'vbox',
            items:	[{
                layout:'fit',
                width: '100%',
                flex:2,
                items: tree
            },{
                layout:'fit',
                width: '100%',
                flex: 2,
                title:_('Available Elements'),
                items: tree.getAvailableElementsList(false)
            }]
        });
    };

    var registerEvents = function(filterPanel,cronkPanel) {
        // resizing must be done manually here
        var resizePanelHandler = function(resizedCmp) {
            var width = resizedCmp.getWidth();
            filterPanel.setWidth(width);
            filterPanel.items.each(function(cmp_child) {
                cmp_child.width = width;
                cmp_child.doLayout();
            });
            filterPanel.doLayout();
        };
        cronkPanel.on("resize",resizePanelHandler);
        
        // register cleanup functions
        grid.on({
            "hide" : cronkPanel.resetCronkView,
            "close" : cronkPanel.resetCronkView,
            "destroy" : cronkPanel.resetCronkView
        });
        grid.findParentByType('tabpanel').on("tabchange",cronkPanel.resetCronkView);
        cronkPanel.on("reset", function() {
            cronkPanel.removeListener("resize",resizePanelHandler);
        });    
    };
};
