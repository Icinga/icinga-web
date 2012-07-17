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

Ext.ns('Icinga.Cronks.util');

(function() {
    
    "use strict";
    
    
    /*
     * Window component to generate links for
     * cronks
     */
    Icinga.Cronks.util.CronkUrlWindow = Ext.extend(Ext.Window, {
        id: 'icinga-cronk-url-preview',
        width: 500,
        hidden: true,
        title: 'Cronk Url',
        layout: 'form',
        padding: 10,
        baseUrl: null,
        separator: '/',
        
        initComponent: function() {
            
            this.closeAction = 'hide';
            this.resizable = false;
            this.draggable = false;
            
            this.setResetFlag(false);
            
            this.bbar = ['->', {
                text: _('Close'),
                iconCls: 'icinga-action-icon-cancel',
                handler: function(button, event) {
                    this.hide();
                },
                scope: this
            }, {
                
            }];
            
            Icinga.Cronks.util.CronkUrlWindow.superclass.initComponent.call(this);
            
            this.textField = Ext.create({
                xtype: 'textarea',
                name: 'cronkUrl'
            });
            
            this.textField.on('focus', function(field) {
                field.selectText();
            }, this, {delay: 100});
            
            this.checkBox = Ext.create({
                xtype: 'checkbox',
                name: 'cronkReset',
                boxLabel: _('Check here to open this cronk only and close all others'),
                handler: function(box, checked) {
                    this.setResetFlag(checked);
                    this.buildCronkUrl();
                },
                scope: this
            });
            
            this.add([{
                xtype: 'fieldset',
                title: 'Url for the cronk',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                height: 120,
                items: [this.textField, this.checkBox]
            }]);
            
            this.doLayout();
            
            this.render(Ext.getBody());
        },
        
        setCronkId: function(cronkid) {
            this.cronkId = cronkid;
        },
        
        getCronkId: function() {
            return this.cronkId;
        },
        
        setResetFlag: function(flag) {
            this.resetFlag = Boolean(flag);
        },
        
        getResetFlag: function() {
            return this.resetFlag;
        },
        
        setBaseUrl: function(baseUrl) {
            this.baseUrl = baseUrl;
        },
        
        getBaseUrl: function() {
            return this.baseUrl;
        },
        
        /*
         * Concatenate our url based on parameters
         * @param {String} id of the cronk
         * @oaram {Boolean} Adds reset parameter
         */
        getCronkUrl: function() {
            var url = AppKit.util.Config.get('base');
            url += this.baseUrl + this.separator + this.getCronkId();
            
            if (this.getResetFlag() === true) {
                url += '?single=true';
            }
            
            return url;
        },
        
        /*
         * Call generation method and adds
         * value to the text box
         */
        buildCronkUrl: function() {
            this.textField.setValue(this.getCronkUrl());
        },
        
        /*
         * Upades the content of the form
         * @param {Object} Cronk Structure
         */
        update: function(o) {
            this.setTitle(String.format(_('Url for {0}'), o.org_name));
            this.setCronkId(o.cronkid);
            this.setResetFlag(false);
            this.buildCronkUrl();
        }
    });
})();