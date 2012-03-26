Ext.ns("LConf.PropertyGrid");
Ext.ns("LConf.PropertyGrid").PropertyGridExtender = function(colModel) {
    this.colModel = colModel;
    this.actionColumns = null;

    this.extendColumns = function() {
        for(var i in LConf.PropertyGrid.Extensions) {
            var extension = LConf.PropertyGrid.Extensions[i];
            
            switch(extension.xtype) {
                case 'button':
                case 'action':
                    this.addAction(extension);
                    break;
                case 'column':
                    // not implemented yet
                    break;

            }
        }
        if(Ext.isArray(this.actionColumns)) {
            this.colModel.push({
                xtype: 'actioncolumn',
                items: this.actionColumns
            })
        }
    };

    var objectMatches = function(store,action) {
        if(typeof action.appliesOn !== "object")
            return true;
        if(typeof action.appliesOn.object !== "object")
            return true;
        var objectSelector = action.appliesOn.object;
       
 
        for(var selector in objectSelector) {
            var currentObjectSelector = objectSelector[selector];
            if(!Ext.isArray(currentObjectSelector))
                currentObjectSelector = [currentObjectSelector];
            
            for(var i=0;i<currentObjectSelector.length;i++) {
                var currentObject = currentObjectSelector[i];
                var property = new RegExp(currentObject,"i");
                selector = new RegExp(selector,"i");
                var match = false;
                // test if property name and value matches
                store.each(function(record) {
                    if(selector.test(record.get("property"))) {
                        if(property.test(record.get("value"))) {
                            match = true;
                            return false;
                        }
                    }
                    return true;
                });

                if(match === true)
                    return true;
            }
        }
        return false;
    }

    var propertyMatches = function(record,action) {
        
        if(typeof action.appliesOn !== "object")
            return true;
        if(typeof action.appliesOn.properties === "undefined")
            return true;
        
        var propertySelector = action.appliesOn.properties;

        if(!Ext.isArray(propertySelector))
            propertySelector = [propertySelector];
        for(var i=0;i<propertySelector.length;i++) {

            var propertyRegExp = new RegExp(propertySelector[i],"i");
            if(propertyRegExp.test(record.get("property")))
                return true;
        }
        return false;

    }

    this.addAction = function(action) {
        if(this.actionColumns === null)
            this.actionColumns = [];
        var item = this.actionColumns.length-1;
        this.actionColumns.push({
            tooltip: action.qtip,
            getClass: function(v, meta, rec,row,col,store) {
               
                if(objectMatches(store,action) && propertyMatches(rec,action)) {
                    this.record = rec;
                    
                    return "icon-16 "+action.iconCls;
                }
                v = "";
                return "";
            },
            handler: action.handler

        });
    }
   
    
};