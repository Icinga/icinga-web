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

Ext.ns('Cronk.grid');

Cronk.grid.InfoIconColumnRenderer = new (function () {
    
    var buildIcon = function(iconCls, title) {
        return Ext.DomHelper.createDom({
            tag : 'div',
            cls : 'x-icinga-info-icon ' + iconCls,
            'ext:qtip' : title
        });
    }
    
    var buildIconFrame = function(data, element) {
        // element.removeClass('icinga-icon-throbber');
        
        if (data.check_type == 'passive') {
            element.appendChild(buildIcon('icinga-icon-info-passive', _('Accepting passive only')));
        } else if (data.check_type == 'disabled') {
            element.appendChild(buildIcon('icinga-icon-info-disabled', _('Check is disabled')));
        }
        
        if (data.in_downtime == true) {
            element.appendChild(buildIcon('icinga-icon-info-downtime', _('Object in downtime')));
        }
        
        if (data.is_flapping == true) {
            element.appendChild(buildIcon('icinga-icon-info-flapping', _('Object is flapping')));
        }
        
        if (data.notification_enabled == false) {
            element.appendChild(buildIcon('icinga-icon-info-notifications-disabled', _('Notifications for this object are disabled')));
        }
        
        if (data.problem_acknowledged == true) {
            element.appendChild(buildIcon('icinga-icon-info-problem-acknowledged', _('Problem has been acknowledged')));
        }
        
    }
    
    var updateContent = function(data, type, columns) {
        if (data.success == true) {
            Ext.iterate(data.rows, function(oid, obj, arry) {
                var id = String.format('object-info-icon-{0}-{1}', type, oid);
                if (columns.contains(id)) {
                    var element = columns.item(columns.indexOf(id));
                    buildIconFrame(obj, element);
                }
            }, this);
        }
    }
    
    var loadInfoData = function() {
        var columns = this.grid.getEl().select("div.object-info-icon-cell");
        if (columns.getCount()) {
            
            var type = "";
            var oids = [];
            var re = new RegExp(/^[\w-]+-(\d+)-(\d+)$/);
            var test = []
            columns.each(function(el, c, idx) {
                test = re.exec(el.id);
                oids.push(test[2]);
            }, this);
            
            type = test[1];
            
            Ext.Ajax.request({
                cancelOn: {
                    component: this.grid.getStore(),
                    event: 'beforeload'
                },
                url : AppKit.util.Config.get('path') + '/modules/appkit/dispatch',
                params : {
                    module : 'Cronks',
                    action : 'Provider.ObjectInfoIcons',
                    params : Ext.encode({
                        type : type,
                        oids : oids.join(','),
                        connection: this.grid.selectedConnection
                    })
                },
                success : function(response, opts) {
                    //try {
                        var data = Ext.decode(response.responseText);
                        updateContent(data, type, columns);
                    //} catch (e) {
                        //AppKit.log('Could not decode object info data ' + e);
                    //}
                    
                },
                scope : this
            })
        }
    }
    
    this.init = function(grid, c) {
        this.grid = grid;
        this.grid.getStore().on('load', loadInfoData, this);
    }
    
    this.infoColumn = function(cfg) {
        return function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.DomHelper.markup({
                tag : 'div',
                cls : 'object-info-icon-cell', // icinga-icon-throbber icon-16
                id : 'object-info-icon-' + cfg.type + '-' + value
            });
        };
    };
    
})();
