Ext.ns('Cronk.grid');


Cronk.grid.CommentColumnRenderer = new (function () {
     var buildIdFilter = function(field,ids) {
        var filter = {
            type: 'OR',
            field: []
        };
        for(var i=0;i<ids.length;i++) {
            if(!ids[i])
                continue;
            filter.field.push({
                type: 'atom',
                field: [field],
                method: ['='],
                value: [ids[i]]
            });
        }
        return filter;
    };
    
    var clearThrobberFields = function(grid,selector) {
        var throbber = grid.getEl().select(selector);
        throbber.each(function(element) {
            element.remove();
        });
    };	
    
    var getIdsFromJSON = function(json, fieldname) {
        var ids = [];
        if(!fieldname)
            return ids;
        for(var i=0;i<json.length;i++) {
            var obj = json[i];
            if(obj[fieldname]) {
                ids.push(obj[fieldname]);
            } else if(obj[fieldname.toUpperCase()])
                ids.push(obj[fieldname.toUpperCase()]);
        }
        return ids;
    };

    var createCommentButtons = function(grid) {
        var ids = grid.commentIds;
        var throbber = grid.getEl().select('div[comment_source]');
        throbber.each(function(elem) {
            if(ids.indexOf(elem.getAttribute("comment_source")) > -1)
                // elem.replaceClass('icinga-icon-throbber','icinga-icon-comment');
                elem.addClass('icinga-icon-comment');
            else 
                elem.remove();
        });
    };



    var requestComments = function(grid,field,ids) {
        Ext.Ajax.request({
            url: AppKit.c.path+"/web/api/json",
            params: {
                target: 'servicecomment',
                
                filters_json: Ext.encode(buildIdFilter(field,ids)),
                "columns[0]": field
            }, 
            success: function(response) {
                try {
                    var json = Ext.decode(response.responseText);
                    grid.commentIds = getIdsFromJSON(json.result,field);
                    
                    createCommentButtons(grid); 
                } catch(e) {
                    AppKit.log("Loading comments failed : "+e);
                    clearThrobberFields(grid,'div[comment_source]'); 
                }
             
            },
            failure: Ext.createDelegate(clearThrobberFields,this,[grid,'div[comment_source]']),
            scope: this
        }); 
    }; 

    this.init = function(grid, c) {
        grid.commentIds = [];
        var store = grid.getStore();
        store.on("load",function() {
            var target;
            var field;
            
            var ids = [];

            store.each(function(record) { 
                target = record.comment_target;
                field = record.comment_field;     
                ids.push(record.json[field]);
            },this);
            if(ids.length < 1)
                return true; 
            requestComments(grid,field,ids);
        },this);
        

        if (Ext.isEmpty(c.column_name)) {
            throw("initCommentEventHandler: Need arguments->column_name to determine fields");
        }
        
        grid.on('cellclick', function(lGrid, rowIndex, columnIndex, e) {
            var column_name = lGrid.getColumnModel().getDataIndex(columnIndex);
            if (column_name == c.column_name) {
                var record = grid.getStore().getAt(rowIndex); 
                var cell = lGrid.getView().getCell(rowIndex,columnIndex);
                var id = record.get(record.comment_field);
                if(lGrid.commentIds.indexOf(id) < 0)
                    return false;
                Icinga.util.SimpleDataProvider.createToolTip({
                    target: cell,
                    title: Cronk.grid.ColumnRendererUtil.applyXTemplate(lGrid, rowIndex, c.title),
                    width: c.width || 400,
                    filter: [{key: 'object_id', value: id}],
                    srcId: c.sourceId || 'comments'
                });
                
            }
        }, this);
    };

    this.commentColumn = function(cfg) {
        return function(value, metaData, record, rowIndex, colIndex, store) {
            var my = cfg;
            record.comment_field    = my.target_field;
            record.comment_target   = my.target;
            return '<div class="icon-16" comment_source="'+value+'"></div>'; // icinga-icon-throbber
        };
    };

      
})();
