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

/*global Ext: false, Icinga: false, _: false */
(function() {
    "use strict";

    Ext.ns("Icinga.Cronks.Tackle.ExternalReferences").PreviewFrame = Ext.extend(Ext.Panel,{
        flex: 2,
        layout: 'fit',
        constructor: function(cfg) {
            cfg = cfg || {};
            this.anchorBtn = new Ext.Button({
                iconCls: 'icinga-icon-anchor',
                text: _('View in new page'),
                handler: this.openURL,
                scope:this,
                disabled: true
            })
            cfg.tbar = new Ext.Toolbar({
                items: [this.anchorBtn]
            });

            
            Ext.Panel.prototype.constructor.call(this,cfg);
        },
        initComponent: function() {
            Ext.Panel.prototype.initComponent.call(this);
         
            this.iFrameEl = Ext.DomHelper.createDom({
                tag: 'iframe',
                style: {
                    width: '100%',
                    height: '100%'
                }
            });
            var iFrame = new Ext.BoxComponent({contentEl: this.iFrameEl});
            this.add(iFrame);
        },
        reset: function() {
            this.setContentURL(null);
        },
        openURL: function() {
            window.open(this.iFrameEl.src);
        },
        setContentURL : function(url) {
            if(url != null) {
                this.anchorBtn.setDisabled(false);
            } else {
                this.anchorBtn.setDisabled(true);
            }
            this.iFrameEl.src = url;
        
        }

    });
})();