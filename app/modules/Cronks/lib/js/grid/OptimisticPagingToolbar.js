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
Ext.ns("Cronk.grid");

(function () {

    "use strict";

    Cronk.grid.OptimisticPagingToolbar = (Ext.extend(Ext.PagingToolbar, {
        afterPageText: "",
        onLoad : function(store,result,options) {
            Ext.PagingToolbar.prototype.onLoad.apply(this,arguments);
            if(!this.rendered){
                return;
            }
            this.first.setDisabled(false);
            this.prev.setDisabled(false);
        
            this.next.setDisabled(!Ext.isArray(result) || result.length < (options.limit || 25));

            this.last.setDisabled(true);
            this.last.hide();
        },

        initComponent : function() {
            Ext.PagingToolbar.prototype.initComponent.apply(this,arguments);

            this.first.setDisabled(false);
            this.prev.setDisabled(false);
            this.next.setDisabled(false);
            this.last.setDisabled(true);
            this.last.hide();
        },
        // private
        onPagingKeyDown : function(field, e){
            var k = e.getKey(), d = this.getPageData(), pageNum;
            if (k == e.RETURN) {
                e.stopEvent();
                pageNum = this.readPage(d)-1;
                if(pageNum !== false){
                    this.doLoad(pageNum * this.pageSize);
                }
            }else if (k == e.HOME || k == e.END){
                e.stopEvent();
                pageNum = k == e.HOME ? 1 : d.page;
                field.setValue(pageNum);
            }else if (k == e.UP || k == e.PAGEUP || k == e.DOWN || k == e.PAGEDOWN){
                e.stopEvent();
                if((pageNum = this.readPage(d))){
                    var increment = e.shiftKey ? 10 : 1;
                    if(k == e.DOWN || k == e.PAGEDOWN){
                        increment *= -1;
                    }
                    pageNum += increment;
                
                    field.setValue(pageNum);
                
                }
            }
        }
    }));

})();
