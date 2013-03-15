/*jshint curly:true */
/*global Ext:true,_:true,AppKit:true,_:true */
(function() {
    "use strict";
    var tpl = new Ext.XTemplate(
        '<div class="statusmap_head" >',
            '<div class="icon-24 icinga-icon-host" ><div style="width:200px;margin-left:24px;padding-top:4px">',
                '<a subgrid="icinga-host-template:0:{HOST_OBJECT_ID}:{HOST_NAME}">{HOST_NAME}</a>',
            '</div></div>',
            '{[ Icinga.StatusData.wrapElement("host",values.HOST_CURRENT_STATE) ]}',                
            '<br/>',
            '<tpl if="notifications_enabled !== undefined">',
                '<div style="clear:both;float:none">',
                '<tpl if="notifications_enabled==0">',
                    '<div style="float:left" ext:qtip="'+_('Notifications disabled')+'" class="icon-24 icinga-icon-info-notifications-disabled"></div>',
                '</tpl>',
                '<tpl if="scheduled_downtime_depth > 0">',
                    '<div style="float:left" ext:qtip="'+_('In Downtime')+'" class="icon-24 icinga-icon-info-downtime"></div>',
                '</tpl>',
                '<tpl if="problem_acknowledged == 1">',
                    '<div style="float:left" ext:qtip="'+_('Problem acknowledged')+'" class="icon-24 icinga-icon-info-problem-acknowledged"></div>',
                '</tpl>',
                '</div><br/>',
                '<div style="padding:5px;border:1px solid #dedede;background-color:#fff;width:90%;clear:both;float:none">',
                    '<table>',
                        '<tr>',
                            '<td style="width:120px">'+_("Alias")+':</td><td>{HOST_ALIAS}</td>',
                        '</tr>',
                        '<tr>',
                            '<td>'+_('Instance')+':</td><td>{INSTANCE_NAME}</td>',
                        '</tr>',
                        '<tr>',
                            '<td>'+_('Last check')+':</td><td>{HOST_LAST_CHECK}</td>',
                        '</tr>',
                        '<tr>',
                            '<td>'+_('Next check')+':</td><td>{HOST_NEXT_CHECK}</td>',
                        '</tr>',
                        '<tr>',
                            '<td>'+_('Check attempt')+':</td><td>{HOST_CURRENT_CHECK_ATTEMPT} of {HOST_MAX_CHECK_ATTEMPT}</td>',
                        '</tr>',

                    '</table>',
                '</div>',
                '<br>Check output:<br/>',
                '<div ext:qtip="{HOST_OUTPUT}" style="padding:5px;border:1px solid #dedede;background-color:#fff;width:90%;clear:both;float:none">',
                    '{[ Ext.util.Format.ellipsis(values.HOST_OUTPUT,200) ]}</span>',
                '</div><br/>',
            '</tpl>',
        '</div>'

    );
        
    var StatusMapGraph = function() {
        this.url = "";
        this.refreshTime = 5000;
        this.connection = "icinga";
        this.detailPanel = null;
        this.centerIsRoot = false;
        this.init = {};
        this.setup = function(cmp,cfg) {
            Ext.apply(this,cfg);
            this.parentCmp = cmp;
            this.setupRGraph();
            
            this.tooltipWnd = new Ext.Window({
                hidden:true,
                width:400,
                height:240,
                closeAction:'hide',
                resizable: false,
                draggable: false,
                closable: false,
                tpl:tpl,
                forceLayout: true,
                constrain: cmp.getEl(),
                style: {
                    backgroundColor: '#dedede',
                    opacity: 0.9
                },
                unstyled: true,
                padding: 5
            });
        };
        
        this.setCenterIsRoot = function(val,noLoad) {
            this.centerIsRoot = val;
            if(!noLoad) {
                this.sync();
                this.parentCmp.ownerCt.fireEvent("graphChange");
            }
        };
    
        this.setConnection = function(connection) {
            this.connection = connection;
            this.parentCmp.ownerCt.fireEvent("graphChange");
        };
        
        
        this.setNodeColor = function(node) {
            switch (node.data.relation.HOST_CURRENT_STATE) {
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
        };
        
        this.updateInfoArea = function(node) {

            this.detailPanel.update(Ext.applyIf(node.data.relation,{
                notifications_enabled: 1,
                HOST_OUTPUT: '-',
                INSTANCE_NAME: '-',
                HOST_CURRENT_CHECK_ATTEMPT: '-',
                HOST_MAX_CHECK_ATTEMPT: '-',
                HOST_LAST_CHECK: '-',
                HOST_NEXT_CHECK: '-',
                problem_acknowledged: 0,
                scheduled_downtime_depth: 0,
                HOST_ALIAS: node.name
            }));
                
           
        };
        
        this.setLabel = function(domElement,node) {
            var el = new Ext.Element(domElement);
            el.setStyle({
                display: 'visible',
                cursor: 'pointer',
                fontSize: "1.2em"
            });
            if (node._depth <= 1) {
                el.setStyle({
                    color: "#000000"
                });
            } else if (node._depth <= 3) {
                el.setStyle({
                    color : "#000000"
                });
            } else {
                el.setStyle("display","none");
            }
            var style = domElement.style;
            var left = parseInt(style.left,10);  
            var w = domElement.offsetWidth;  
            style.left = (left - w / 2) + 'px';
            
        };
        
        this.applyNodeEvents = function(domElement,node) {
            var el = new Ext.Element(domElement);

            el.on("mouseenter",function(ev,et) {
                this.tooltipWnd.setPagePosition(ev.getPageX()+25,ev.getPageY()+25);
                this.tooltipWnd.show();
                this.tooltipWnd.update(node.data.relation);
            },this);
            el.on("mouseleave",function(ev,et) {
                this.tooltipWnd.setPagePosition(ev.getPageX(),ev.getPageY());
                this.tooltipWnd.hide();
            },this);
        };
        
        this.showNodeInfoIcons = function(domElement,node) {
            var el = new Ext.Element(domElement);
            var tpl = new Ext.XTemplate("<div class='icon-16 icinga-icon-info-{cls}' style='margin-left:2px;float:left' ext:qtip='{qtip}'></div>");
            var data = node.data.relation;
            var ctrDOM = Ext.DomHelper.append(
                el.dom,
                {
                    tag: 'div',
                    style: 'clear:both;float:none'
                }
            );
            if(data.notifications_enabled == 0) {
                tpl.append(ctrDOM,{cls: 'notifications-disabled','qtip':_('Notifications disabled')})
            }
            if(data.problem_acknowledged == 1) {
                tpl.append(ctrDOM,{cls: 'problem-acknowledged','qtip':_('Problem acknowledged')})
            }
            if(data.scheduled_downtime_depth > 0) {
                tpl.append(ctrDOM,{cls: 'downtime','qtip':_('In downtime')})
            }
        }
        
        this.getSubJsonFromObjectId = function(id,sub) {
            sub = sub || this.currentJson;
            if(sub.data.relation.HOST_OBJECT_ID == id)
                return sub;
            for(var i=0;i<sub.children.length;i++) {
                var obj = this.getSubJsonFromObjectId(id,sub.children[i]);
                if(obj)
                    return obj;
            }
            return null;
        }
        
        this.createLabel = function(domElement, node){  
            domElement.innerHTML = node.name;
            var that=this;
            (new Ext.Element(domElement)).on("click",function(){
                if(node.data.relation.HOST_OBJECT_ID)
                    this.centeredNode = node.data.relation.HOST_OBJECT_ID;
                this.rgraph.onClick(node.id, {  
                    onComplete: function() {
                        // only expand automatically if there is enough room
                        if(that.parentCmp.getWidth() > 500)
                            that.detailPanel.getPanel().expand();
                        if(that.centerIsRoot)
                            that.sync();
                    }
                });
            },this);  
            if(node.data.relation.HOST_OUTPUT) {
                 this.applyNodeEvents(domElement,node);
                 this.showNodeInfoIcons(domElement,node);

            }
        }
    
        this.setupRGraph = function() {
            this.rgraph = new $jit.RGraph({
                injectInto: this.parentCmp.body.dom,            
                Navigation: {
                    enable: true,
                    panning: "avoid node",
                    zooming: 20
                },
                width: 1,
                height: 1,
                background: {
                    CanvasStyles: {
                        strokeStyle: "#e0e0e0"
                    }
                },
                Node: {
                    overridable: true,
                    color: "#ccddee"
                },
                Edge: {
                    color: "#56a5ec"
                },
                onCreateLabel: this.createLabel.createDelegate(this),
                onBeforeCompute: this.updateInfoArea.createDelegate(this),
                onPlaceLabel: this.setLabel.createDelegate(this),
                onBeforePlotNode: this.setNodeColor
            });
    
        };
    
        this.findNodeByObjectId = function (misc, oid) {
            var node = null;
            Ext.each(misc, function (item) {
                if (item.data.relation.HOST_OBJECT_ID == oid) {
                    node = item;
                    return false;
                } else if (Ext.isDefined(item.children) && item.children.length > 0) {
                    node = this.findNodeByObjectId(item.children, oid);
                }
            }, this);
            return node;
        };

        this.findNodeById = function (misc, oid) {
            var node = null;
            Ext.each(misc, function (item) {
                if (item.id === oid) {
                    node = item;
                    return false;
                } else if (Ext.isDefined(item.children) && item.children.length > 0) {
                    node = this.findNodeById(item.children, oid);
                }
            }, this);
            return node;
        };
        this.onDataUpdate = function(resp) {
            this.currentJson = Ext.decode(resp.responseText);
            if(typeof this.init.centerIsRoot !== "undefined") {
                this.centerIsRoot = this.init.centerIsRoot || this.centerIsRoot;
            }
            this.redraw()
            if(typeof this.init.centeredNode !== "undefined") {
                var id = this.init.centeredNode;
                this.centeredNode = id;
                this.centerObjectId.defer(400,this,[id]);
            }
            this.init = {};
        }
        this.redraw = function() {
            if(this.centerIsRoot && this.centeredNode)
                this.currentJson = this.getSubJsonFromObjectId(this.centeredNode);
            this.rgraph.loadJSON(this.currentJson);
            this.rgraph.refresh();
            this.rendered = true;
            this.doLayout();
        }
        this.centerObjectId = function(node) {
            this.centeredNode = node;
            var n = this.findNodeByObjectId(this.currentJson,this.centeredNode);
            if(n) {
                this.rgraph.onClick(n.id);
            }
            this.parentCmp.ownerCt.fireEvent("graphChange");
        }
        
        this.sync = function() {
            Ext.Ajax.request({
                url: this.url,
                params: {},
                success: this.onDataUpdate,
                failure: function(resp) {
                
                },
                scope: this
            });
        }
    
        this.doLayout = function() {
            
            if(!this.rendered)
                return;
            if(this.centeredNode) {
                this.centerObjectId(this.centeredNode);
            }
            this.rgraph.canvas.resize(this.parentCmp.getWidth(),this.parentCmp.getHeight());
        }
    
        this.refreshTask = {
            run: this.sync.createDelegate(this),
            interval: (this.refreshTime * 1000)
        }

    }

    

    Ext.ns("Icinga.Cronks").StatusMapPanel = function(cfg) {
     
        cfg.detailPanel = new Icinga.Cronks.StatusMapDetailPanel(tpl);
        var graph = new StatusMapGraph();
        var centerToggleBtn = new Ext.Button({
            xtype: 'tbbutton',
            text: _('Use centered node as root'),
            enableToggle:true,
            iconCls: 'icinga-icon-structure',
            toggleHandler: function (item, state) {
                graph.setCenterIsRoot(state);
            }
        });
        var panel = new Ext.Panel({
            stateful: true,
            stateId: cfg.stateuid,
            layout: 'border',
            getState: function() {
                
                var state = {
                    centeredNode: graph.centeredNode,
                    centerIsRoot: graph.centerIsRoot
                }
                return state;
            },
            applyState: function(o) {
                AppKit.log("apply state",o);
                if(typeof o.centerIsRoot !== "undefined") {
                    graph.init.centerIsRoot = o.centerIsRoot;
                    centerToggleBtn.toggle(true,true);
                }
                if(typeof o.centeredNode !== "undefined")
                    graph.init.centeredNode = o.centeredNode;
                
            },
            stateEvents: ['autorefreshchange', 'activate', 'graphChange'],
            events: {
                graphChange: true
            }, 
            items: [{
                region: 'center',
                xtype: 'panel',
                layout:'fit',
                tbar: [{
                    iconCls: 'icinga-icon-application-edit',
                    text: _('Settings'),
                    menu: [{
                        text: _('Autorefresh'),
                        xtype: 'menucheckitem',
                        checked:true,
                        checkHandler: function (item, state) {
                            
                            var tr = AppKit.getTr();
                            if (state === true) {
                                tr.start(graph.refreshTask);
                            } else if (state === false) {
                                tr.stop(graph.refreshTask);
                            }
                        },
                        scope: this
                    }]
                },centerToggleBtn],
                listeners: {
                    render: function(cmp) {
                        graph.setup(cmp,cfg);
                        cmp.graph = graph;
                        cmp.graph.sync();
                        
                        var tr = AppKit.getTr();
                        tr.start(cmp.graph.refreshTask);
                    },
                    resize: function(cmp) {
                        
                       cmp.graph.doLayout.apply(cmp.graph,arguments);
                    },
                    scope: this
                }
            },cfg.detailPanel.getPanel()]
        });
        panel.centerNodeByObjectId = function(id) {
            return graph.centerObjectId(id);
        }
        return panel;
    };


})();