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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns('Cronk.grid');

(function() {
    "use strict";
    
    /**
     * Renderer to insert widgets into grids
     */
    Cronk.grid.WidgetRenderer = {
            /**
             * This method take a button configuration and install
             * a button into the grid column if used as a cell renderer.
             * 
             * <pre><code>
             * {
                        header: "",
                        dataIndex: "catid",
                        width: 120,
                        fixed: true,
                        renderer: Cronk.grid.WidgetRenderer.button({
                            iconCls: 'icinga-icon-lock',
                            text: _('Permissions'),
                            handler: function(b, e, record) {
                                console.log("clicked", record);
                            },
                            scope: this
                        })
                    }
             * </code></pre>
             * 
             * The handler function takes folowing parameters:
             * 
             * <ul>
             * <li>{@link Ext.Button button}</li>
             * <li>{@link Ext.EventObject event}</li>
             * <li>{@link Ext.data.Record record}</li>
             * </ul>
             * 
             * @param {Object} cfg
             * @returns {Function} The renderer
             */
            button: function(cfg) {
                
                var createButton = function(id, record) {
                    var delegate = Ext.createDelegate(
                        (Ext.isEmpty(cfg.handler)===true) ? Ext.emptyFn : cfg.handler, 
                        (Ext.isEmpty(cfg.scope)===true) ? this : cfg.scope,
                        [record],
                        true
                    );
                    
                    var buttonConfig = Ext.apply(cfg, {
                        id: id,
                        handler: delegate
                    });
                    
                    var button = new Ext.Button(buttonConfig);
                    
                    button.render(Ext.getBody(), id);
                };
                
                return function (value, o, record, rowIndex, colIndex, store) {
                    var id=Ext.id();
                    createButton.defer(1, this, [id, record]);
                    return String.format('<div id="{0}"></div>', id);
                }
            }
    };
    
})();