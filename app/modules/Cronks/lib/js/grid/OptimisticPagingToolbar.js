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
