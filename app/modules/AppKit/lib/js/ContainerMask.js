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

Ext.namespace('Ext.ux.plugins');
Ext.ux.plugins.ContainerMask = function(opt) {
    var options = opt||{};

    return {
        init: function(c) {
            Ext.applyIf(c,{
                showMask : function(msg, msgClass, maskClass){
                    var el;
                    if(this.rendered && (el = this[options.el] || Ext.get(options.el) || this.getEl?this.getEl():null)){
                      el.mask.call(el,msg || options.msg, msgClass || options.msgClass, maskClass || options.maskClass);
                    }
                },
                hideMask : function(){
                    var el;
                    if(this.rendered && (el = this[options.el] || Ext.get(options.el) ||  this.getEl?this.getEl():null)){ 
                      el.unmask.call(el);
                    }
                }
            });
            if(options.masked){ 
                c.on('render', c.showMask.createDelegate(c,[null]) ,c, {delay:10, single:true}) ; 
            }
        }
    };
};