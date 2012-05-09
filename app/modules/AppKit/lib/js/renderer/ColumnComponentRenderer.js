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

/* 
 * ColumnRenderer that allows to inject components to columns
 * @param Ext.Component     The grid that contains this column (the "render" event is needed)
 * @param Function|Object   The component to add, either as json object or as a function if cfg is provided
 * @param cfg               (optional) If cmp is a function, cfg is the json used for construction
 *
 * Example
 * ..
 * sm: new Ext.grid.ColumnModel({
 *      header: 'I am a component described by json',
 *      renderer: AppKit.renderer.ColumnComponentRenderer({
 *          xtype: 'button',
 *          text: 'Hi'
 *      });
 * }, {
 *      header: 'I am a component from a function',
 *      renderer: AppKit.renderer.ColumnComponentRenderer(Ext.Button,{
 *          text: 'Hi'
 *      });
 * })
 */
Ext.onReady(function() {
    var maxDepth = 5; // maximum copy/resolve depth of objects

    
    
    Ext.ns("AppKit.renderer").ColumnComponentRenderer = function(grid, cmp,cfg, maxDepth) {

        return function(value, metaData, record, rowIndex, colIndex, store) {
            var id = Ext.id();
            cfg = cfg || cmp || {};
            cfg.baseArgs = {
                value: value,
                metaData: metaData,
                record: record,
                rowIndex: rowIndex,
                colIndex: colIndex,
                store: store
            };
            
            var toRender = null;
            
            if(Ext.isObject(cmp)) {
                toRender = new Ext.Component(cfg);
            } else if(Ext.isFunction(cmp)) {
                toRender = new cmp(cfg);
            }
            grid.getStore().on("load", function() {
                toRender.render(Ext.get(id));
            });
            return "<div id='"+id+"'></div>";
        };
    };

    
});