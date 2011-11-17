/* 
 * ColumnRenderer that allows to inject components to columns
 *
 * @param Function|Object   The component to add, either as json object or as a function if cfg is provided
 * @param cfg               (optional) If cmp is a function, cfg is the json used for construction
 *
 * Example
 * ..
 * sm: new Ext.grid.ColumnModel({
 *      header: 'I am a component described by json',
 *      renderer: AppKit.renderer.ColumnComponentRenderer({
 *          xtype: 'button',
 *          text: 'Hi'
 *      });
 * }, {
 *      header: 'I am a component from a function',
 *      renderer: AppKit.renderer.ColumnComponentRenderer(Ext.Button,{
 *          text: 'Hi'
 *      });
 * })
 */
Ext.onReady(function() {
    Ext.ns("AppKit.renderer").ColumnComponentRenderer = function(cmp,cfg, maxDepth) {
        maxDepth = maxDepth || 5;

        var tokens = {
            "%VALUE%"       : 0,
            "%METADATA%"    : 1,
            "%RECORD%"      : 2,
            "%ROWINDEX%"    : 3,
            "%COLINDEX%"    : 4,
            "%STORE%"       : 5
        };


        var resolveVals = function(obj,args,curDepth) {
            
            curDepth = curDepth || 0;
            if(curDepth > maxDepth)
                return null;
            
            if(Ext.isArray(obj)) {
                for(var i=0;i<obj.length;i++) {

                    if(typeof obj[i] === "string" && typeof tokens[obj[i]] !== "undefined") {
                        obj[i] = args[tokens[obj[i]]]; // resolve token from arglist
                    } else if(typeof obj[i] === "object") {
                        obj[i] = resolveVals(obj[i],args,curDepth+1);
                    }                }
            } else if (Ext.isObject(obj)) {
                for(var o in obj) {
                    if(typeof obj[o] === "string"  && typeof tokens[obj[o]] !== "undefined") {
                        obj[o] = args[tokens[obj[o]]]; // resolve token from arglist
                    } else if(typeof obj[o] === "object") {
                        obj[o] = resolveVals(obj[o],args,curDepth+1);
                    }
                }
            } 
            return obj;
        };

        var copyObject = function(obj,curLevel) {
            curLevel = curLevel || 0;

            if(curLevel > maxDepth)
                return null;
            var target = {};
            if(Ext.isArray(obj)) {
                target = [];
                for(var x=0;x<obj.length;x++) {
                    target[x] = copyObject(obj[x],curLevel+1);
                }
                return target;
            } else if(Ext.isObject(obj)) {
                for(var i in obj) {
                    target[i] = copyObject(obj[i],curLevel+1);
                }
                return target;
            } else {
                 return obj;
            }

        };

        return function(value, metaData, record, rowIndex, colIndex, store) {
            var id = Ext.id();
            var start = new Date();
            AppKit.log(id,"Render schedule ",start.getTime(),start);
            var args = arguments;
            var renderFn = function(nrOfTry) {
                var sched = new Date();
                AppKit.log(id,"Render start ",sched.getTime(),sched);
                if(!Ext.get(id)) {
                    // try 5 times and then give up
                    if(nrOfTry++ < 5) {
                        renderFn.defer(100,this,[renderFn,nrOfTry]);
                    }
                    return false;
                }
                
                if(Ext.isObject(cmp)) {
                    var cfgCpy = copyObject(cmp);
                    cfgCpy.renderTo = id;
                    resolveVals(cfgCpy,args);
                    new Ext.Component(cfgCpy);
                } else if(Ext.isFunction(cmp)) {
                    cfg = cfg || {};
                    var cfgCpy = copyObject(cfg);
                    cfgCpy.renderTo = id;
                    cfgCpy = resolveVals(cfgCpy,args);
                    new cmp(cfgCpy);
                }
                var end = new Date();
                AppKit.log(id,"Render finished",end.getTime(),sched);
                AppKit.log(id,"Complete time needed:",end-start);
                AppKit.log(id,"Render time needed:",end-sched);
            }

            renderFn.defer(100,this,[1]);
            return "<div id='"+id+"'></div>";
        }
    }
});


