// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
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
 * Little widget that displays the currents servertime
 *
 */
Ext.ns('Cronk.menu');

Cronk.menu.ReloadStatus = Ext.extend(Ext.menu.Item, {
    STATUS_URL : '/modules/cronks/reloadStatus/json',

    initComponent : function() {
        this.activeClass = '';
        this.iconCls = 'icinga-icon-exclamation-red';
        this.text = '<strong>' +_('Icinga is currently reloading!') + '</strong>';
        this.style = 'margin-right: 10px;';
        this.hidden = true;

        Cronk.menu.ReloadStatus.superclass.initComponent.call(this);

        this.updateTask = {
            run: this.updateDisplay,
            interval: 10000,
            scope:this
        };

        this.highlightTask = {
            run: this.doHighlight,
            interval: 2000,
            scope: this
        };
    },

    updateDisplay : function() {
        Ext.Ajax.request({
            url: AppKit.util.Config.get('path') + this.STATUS_URL,
            method: 'GET',
            success: function(response) {
                var text = response.responseText;
                if (text) {
                    var data = Ext.util.JSON.decode(text);
                    if (!Ext.isEmpty(data.config_reload)) {
                        this.setVisible(data.config_reload);

                        if (this.isVisible()) {
                            this.doHighlight();
                        }
                    }
                }
            },
            scope: this
        })
    },

    doHighlight : function() {
        var ele = this.getEl();
        ele.highlight('FF00FF', {
            attr: 'background-color',
            duration:.6,
            callback: function () {
                ele.stopFx();
            }
        });
    },

    afterRender : function() {
        this.el.dom.qtip = _('No valid status data is available and interface does not respond as usual');

        Cronk.menu.ReloadStatus.superclass.afterRender.apply(this, arguments);

        if (this.getEl()) {
            AppKit.getTr().start(this.updateTask);
        }
    }
});
