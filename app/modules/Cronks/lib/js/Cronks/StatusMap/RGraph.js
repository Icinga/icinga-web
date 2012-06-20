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

Ext.ns('Icinga.Cronks.StatusMap');

function jitAddEvent(obj, type, fn) {
    if (obj.addEventListener) {
        obj.addEventListener(type, fn, false);
    } else {
        obj.attachEvent("on" + type, fn);
    }
};

var JitLog = {
    elem: false,
    write: function(elementId, text) {
        if (!this.elem) {
            this.elem = document.getElementById(elementId);
            
        }
        this.elem.innerHTML = text;
        this.elem.style.left = (500 - this.elem.offsetWidth / 2) + "px";
    }
};

Icinga.Cronks.StatusMap.RGraph = function (config) {

    this.cmp = false;

    this.config = {
        url: false,
        params: {},
        method: "GET",
        timeout: 50000,
        disableCaching: true,
        parentId: false
    };

    this.configWritable = ["url", "params", "method", "timeout", "disableCaching", "parentId"];

    this.elementIds = {
        set: false,
        jitContainer: "jitContainer",
        jitContainerCenter: "jitContainerCenter",
        jitMap: "jitMap",
        jitContainerRight: "jitContainerRight",
        jitDetails: "jitDetails",
        jitLog: "jitLog",
        jitCanvas: "jitCanvas"
    };

    this.elementIdsWrite = ["jitContainer", "jitContainerCenter", "jitMap", "jitContainerRight", "jitDetails", "jitLog", "jitCanvas"];

    this.jitJson = false;
    
    var rgraph = null;

    function jitInit (json, elementIds) {
        var infovis = document.getElementById(elementIds.jitMap);
        if(!infovis)
            return true;
        var panel = Ext.DomQuery.selectNode('.x-panel-body',infovis);       
        if(panel) {
            var pElem = Ext.get(panel);
            pElem.setHeight(infovis.offsetHeight);
            pElem.setWidth(infovis.offsetWidth);
            infovis = panel;
        }

        var w = infovis.offsetWidth, h = infovis.offsetHeight;
        
        rgraph = new $jit.RGraph({
            Node: {
                overridable: true,
                color: "#ccddee"
            },
            Edge: {
                color: "#56a5ec"
            },
            "injectInto": infovis.id,
            "width": w,
            "height": h,
            "background": {
                "CanvasStyles": {
                    "strokeStyle": "#e0e0e0"
                },
                "impl": {
                    "init": function() {},
                    "plot": function(canvas, ctx) {
                        var times = 6, d = 100;
                        var pi2 = Math.PI * 2;
                        for (var i = 1; i <= times; i++) {
                            ctx.beginPath();
                            ctx.arc(0, 0, i * d, 0, pi2, true);
                            ctx.stroke();
                            ctx.closePath();
                        }
                    }
                }
            },
            Navigation: {
                enable: true,
                panning: 'avoid nodes',
                zooming: 20
            },
            onBeforeCompute: function(node){
                JitLog.write(elementIds.jitLog, String.format(_('centering {0} ...'), node.name));
                document.getElementById(elementIds.jitDetails).innerHTML = node.data.relation;
            },
            onAfterCompute: function(){
                JitLog.write(elementIds.jitLog, _('done'));
            },
            onBeforePlotNode:function(node) {
                
                switch (node.data.status) {
                    case "0":
                        node.data.$color = "#00cc00";
                        break;
                    case "1":
                        node.data.$color = "#cc0000";
                        break;
                    case "2":
                        node.data.$color = "#ff8000";
                        break;
                    case "99":
                        node.data.$color = "#aa3377";
                        break;
                }
            },
            onCreateLabel: function(domElement, node){
                domElement.innerHTML = node.name;
                domElement.onclick = function(){
                    rgraph.onClick(node.id);
                };
            },
            onPlaceLabel: function(domElement, node){
                var style = domElement.style;
                style.display = "";
                style.cursor = "pointer";
                if (node._depth <= 1) {
                    style.fontSize = "1em";
                    style.color = "#000000";
                } else if(node._depth <= 3){
                    style.fontSize = "0.9em";
                    style.color = "#505050";
                } else {
                    style.display = "none";
                }
                var left = parseInt(style.left);
                var w = domElement.offsetWidth;
                style.left = (left - w / 2) + "px";
            }
        });

        rgraph.loadJSON(json);
        rgraph.refresh();
    
        document.getElementById(elementIds.jitDetails).innerHTML = rgraph.graph.getNode(rgraph.root).data.relation;
    }
    
    this.getRGraph = function() {
        return rgraph;
    }
    
    this.setConnection = function(connection) {
        this.config.params["connection"] = connection;
    }
    
    this.centerNodeByObjectId = function(oid) {
        var centerFunction = (function() {
            var node = this.findNodeByObjectId(this.jitJson, oid);
            if (Ext.isObject(node)) {
                rgraph.onClick(node.id);
            }
        }).createDelegate(this);
        
        var waitFunction = (function() {
            if (this.jitJson === false) {
                waitFunction.defer(200);
            } else {
                centerFunction();
            }
        }).createDelegate(this);
        
        waitFunction();
    }
    
    this.findNodeByObjectId = function(misc, oid) {
        var node = null;
        Ext.each(misc, function(item) {
            if (item.data.object_id == oid) {
                node = item;
                return false;
            } else if (Ext.isDefined(item.children) && item.children.length > 0) {
                node = this.findNodeByObjectId(item.children, oid);
            }
        }, this);
        return node;
    };
    
    this.findNodeById = function(misc, oid) {
        var node = null;
        Ext.each(misc, function(item) {
            if (item.id == oid) {
                node = item;
                return false;
            } else if (Ext.isDefined(item.children) && item.children.length > 0) {
                node = this.findNodeById(item.children, oid);
            }
        }, this);
        return node;
    };
    
    this.init = function (config) {
        this.setConfig(config);
        this.setElementIds();
        this.createContainer();
        this.getMapData();
    }

    this.setConfig = function (config) {
        var numParams = this.configWritable.length;
        for (var x = 0; x < numParams; x++) {
            var key = this.configWritable[x];
            var value = config[key];
            if (value != undefined) {
                this.config[key] = value;
            }
        }
        if (this.config.parentId != false) {
            this.cmp = Ext.getCmp(this.config.parentId);
            
        }
    }

    this.setElementIds = function () {
        if (!this.elementIds.set) {
            var numElements = this.elementIdsWrite.length;
            for (var x = 0; x < numElements; x++) {
                this.elementIds[this.elementIdsWrite[x]] = this.config.parentId + this.elementIds[this.elementIdsWrite[x]];
            }
            this.elementIds.set = true;
        }
    }

    this.createContainer = function () {
        this.container = new Ext.Container({
            id: this.elementIds.jitContainer,
            autoEl: 'div', 
            layout: 'column',
            defaults: {
                xtype: 'container',
                autoEl: 'div',
                layout: 'auto',
                style: {
                    border: "none"
                }
            },
            items : [{
                id: this.elementIds.jitContainerCenter,
                cls: "jitContainerCenter",
        
                items: {
                    id: this.elementIds.jitMap,
                    cls: "jitMap"
                }
            },{
                id: this.elementIds.jitContainerRight,
                cls: "jitContainerRight",
                items: {
                    id: this.elementIds.jitDetails,
                    cls: "jitDetails"
                }
            },{
                id: this.elementIds.jitLog,
                cls: "jitLog"
            }]
        });
    
        this.cmp.add(this.container);
        this.cmp.doLayout();
    }

    this.showMask = function () {
        this.mask = new Ext.LoadMask(Ext.getBody());
        this.mask.show();           
    }

    this.reloadTree = function() {
        this.showMask();
        
        var root = rgraph.root;
        var node = this.findNodeById(this.jitJson, root);

        Ext.Ajax.request({
            url : this.config.url,
            params : this.config.params,
            method : this.config.method,
            success : function (response, o) {
                this.jitJson = Ext.decode(response.responseText);
                rgraph.loadJSON(this.jitJson);
                rgraph.refresh();
                this.centerNodeByObjectId(node.data.object_id);
                this.mask.hide();
                this.mask.disable();
            },
            failure : this.getMapDataFail,
            callback : this.getMapDataDefault,
            scope: this,
            timeout : this.config.timeout,
            disableCaching : this.config.disableCaching
        });
    };

    this.getMapData = function () {
        this.showMask();
        Ext.Ajax.request({
            url : this.config.url,
            params : this.config.params,
            method : this.config.method,
            success : this.getMapDataSuccess,
            failure : this.getMapDataFail,
            callback : this.getMapDataDefault,
            scope: this,
            timeout : this.config.timeout,
            disableCaching : this.config.disableCaching
        });
    }
    
    this.getMapDataSuccess = function (response, o) {
        this.jitJson = Ext.util.JSON.decode(response.responseText);
        jitInit(this.jitJson, this.elementIds);
        this.mask.hide();
        this.mask.disable();
    }

    this.getMapDataFail = function (response, o) {
        this.jitJson = {};
        this.mask.hide();
        this.mask.disable();
    }

    this.getMapDataDefault = function (options, success, request) {
        this.mask.hide();
        this.mask.disable();
    }




    this.init(config);

};
