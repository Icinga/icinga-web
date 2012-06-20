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

/* 
 * Global error handler for icinga-web
 * .
 */

Ext.ns("AppKit.errorHandler");
(function() {
    AppKit.BugTracker = new function() {
        var errorMsg = function(msg,file,line) {
            this.msg = 'No message available';
            this.file = 'No file available';
            this.line = 'No line available';
            this.stack = 'No stacktrace available';
            this.comment = "<span style='color:#ff0000'>No comment</span>";
            this.time = new Date().toLocaleString();
            try {
                this.msg = msg;
                this.file = file;
                this.line = line;
            } catch(e) {}
        };

        var errorReport = function() {  
            this.getHeader = function() {
                return  ";---------------------------------------------------\n"+
                    ";Icinga Interface Error Report \n"+
                    ";---------------------------------------------------\n\n"+
                    ";Header definitions\n"+
                    "CreationTime = '"+((new Date()).toLocaleString() || 'Unknown')+"'\n"+
                    "URL = '"+(window.location.href || 'Unknown')+"'\n"+
                    "Platform = '"+(navigator.platform || 'Unknown')+"'\n"+
                    "User-Agent = '"+(navigator.userAgent || 'Unknown')+"'\n"+
                    "\n";
            }
            
            this.text = ''
            this.send = function() {}
            this.show = function() {
                this.update();
                var id = Ext.id('errorProtocol');
                (new Ext.Window({
                    title: _('Error report'),
                    autoHeight:true,
                    id: id,
                    width:800,
                    constrain:true,
                    items: [{
                        html: "<div style='height:300px;width:100%;overflow:scroll' class='icinga-codeBox'>"+this.text+"</div>"
                    },{
                        html: "<div style='width:100%;height:100%;font-size:12px;padding:5px;text-align:center'>If this bug is not already reported, feel free to report it at our <a href='https://dev.icinga.org/projects/icinga-web'>Bugtracker</a> (Don't forget to attach this report).</div>"
                    }],
                    buttons: [{
                        text: _('Close'),
                        iconCls: 'icinga-icon-close',
                        handler: function() {
                            Ext.getCmp(id).close();
                        }
                    }]
                })).show(document.body);
            }
            this.update = function() {
                this.buildText();
            }
            
            this.buildText = function() {
                this.text = this.getHeader();
                this.text += ";The following errors occured\n";
                var ctr = 1;
                Ext.each(occuredErrors, function(error) {
                    var textMsg = "[Error "+(ctr++)+"]\n";
                    textMsg += 
                        "Message = '"+error.msg+"'\n"+
                        "File = '"+error.file+"'\n"+
                        "Line = '"+error.line+"'\n"+
                        "Time = '"+error.time+"'\n"+
                        "Comment = '"+error.comment+"'\n\n";
                    this.text += textMsg;
                    
                },this);
                this.text += ";EOF";
            }
            
            this.buildText();

        }

        var occuredErrors = [];
        var suspended = false;
        var showErrors = true;
        var handleError = function(msg,file,line) {
            var curError = new errorMsg(msg,file,line);
            occuredErrors.push(curError);
            if(showErrors) {
                updateErrorDisplay();
            }

        };
        var bugReportField = null;
        var updateErrorDisplay = function() {
            if(!bugReportField)
                setupErrorDisplay();
            else {
                bugReportField.setText(occuredErrors.length);
                Ext.getCmp('menu-navigation').doLayout();
            }
            
        }

        var setupErrorDisplay = function() {
            if (Ext.getCmp('menu-navigation')) {
                var elem = Ext.getCmp('menu-navigation');
                bugReportField = new Ext.Button({
                    text: occuredErrors.length,
                    iconCls: 'icinga-icon-bug',
                    handler: AppKit.BugTracker.showErrorMessageInfoBox
                })
                elem.addItem(bugReportField);
                elem.doLayout();
            }
            
        }

        window.onerror = handleError;
        var clearErrors = function() {
            occuredErrors = [];
            updateErrorDisplay();
        };
        return {
            clearErrors: this.clearErrors,
            
            getErrors: function() {
                return occuredErrors;
            },
            setError: function(msg,file,line) {
                handleError(msg,file,line);
            },

            suspend: function() {
                window.onerror = function() {};
                suspended = true;
            },

            resume: function() {
                window.onerror = handleError;
                suspended = false;
            },

            isSuspended: function() {
                return suspended
            },

            setShowErrors: function(bool) {
                showErrors = bool;
            },

            showErrorMessageInfoBox: function() {
                var data = [];
                var i=0;
                Ext.each(occuredErrors,function(error) {
                    data.push([i++,error.msg,error.file,error.line,error.time,error.comment]);
                })
                var dview = new Ext.DataView({
                    store:new Ext.data.ArrayStore({
                        fields: ['id','msg','file','line','time','comment'],
                        idIndex: 0,
                        data: data,
                        autoDestroy: true
                    }),
                    tpl: new Ext.XTemplate(
                        '<tpl for=".">',
                            '<div ext:qtip="Click to comment this bug" class="icinga-bugBox">',
                                '<b>Message</b>: {msg}<br/>',
                                '<b>File</b>: {file}<br/>',
                                '<b>Line</b>: {line}<br/>',
                                '<b>Occured</b>: {time}<br/>',
                                '<b>Comment</b>: {comment}',
                            '</div>',
                        '</tpl>'),
                    listeners: {
                        click: function(dview,index,node,event) {
                            var error = dview.getStore().getAt(index);
                            Ext.Msg.prompt(
                                _("Comment bug"),
                                _("Please enter a comment for this bug. This could be"+
                                  "<div style='background-color: #ffffff;padding:5px;-moz-border-radius:5px;-webkit-border-radius:5px'><ul style='list-style-type:circle'><li>What did you do when it occured?</li><li>Could you reproduce it? How?</li><li>Did you encounter any problems with the interface when the bug occured</li><li>If it happened after an update: Did the feature work in prior versions?</li></ul></div>"),
                                function(btn,text) {
                                    if(btn != 'ok')
                                        return false;
                                    error.set('comment', encodeURI(text));
                                    dview.refresh();
                                    occuredErrors[index]["comment"] = encodeURI(text);
                                },this,true
                            );
                        },
                        scope:this
                    },
                    overClass:'xover',
                    itemSelector:'div.icinga-bugBox'

                });
                var boxId = Ext.id('box_bug');
                var _this = this;
                var box = new Ext.Window({
                    modal:true,
                    height: 400,
                    constrain:true,
                    width:700,
                    title: _('Bug report'),
                    layout:'auto',
                    id: boxId,
                    items: [{
                        padding:5,
                        html:'<div class="icinga-icon-bug-32" style="padding-left:35px;padding-top:2px;height:32px;overflow:visible"><h2>'+_('Icinga bug report')+'</h2></div>'+
                            '<br/>'+_('The following '+occuredErrors.length+' error(s) occured, sorry for that:')
                    },{
                        layout:'auto',
                        xtype:'panel',
                        collapsible:true,
                        height:250,
                        autoScroll:true,
                        padding:5,
                        items:dview
                    }],

                    buttons: [/*{
                        text: _('Send report to admin'),
                        iconCls: 'icinga-icon-application-form',
                        handler: function() {new errorReport().send();},
                        scope:this
                    },*/{
                        text: _('Create report for dev.icinga.org'),
                        iconCls: 'icinga-icon-information',
                        handler: function() {new errorReport().show();},
                        scope:this
                    },{
                        text: _('Clear errors'),
                        iconCls: 'icinga-icon-delete',
                        handler: function() {
                            clearErrors();
                            Ext.getCmp(boxId).close();
                        },
                        scope:_this
                    },{
                        text: _('Close'),
                        iconCls: 'icinga-icon-cancel',
                        handler: function() {
                            Ext.getCmp(boxId).close();
                        }
                    }]
                }).show(document.body);
            }
        }

    }
    AppKit.AjaxErrorHandler = new function() {
        
        var notifyBoxEnabled = true;
        var bugTrackerReportEnabled = true;
        // set user settings on startup
        

        var trackError = function(msg,src,line,isBug) {
            src = src || 'Unknown';
            line  = line || 'Unknown';
            if(notifyBoxEnabled)
                AppKit.notifyMessage(_('Request failed'),msg);
            if(isBug && bugTrackerReportEnabled)
                AppKit.BugTracker.setError(msg,src,line);
        }

        // Set up error handling for all automatic requests
        Ext.data.DataProxy.on('exception',function(proxy,type,action, options,response, arg) {
            handleError(response,proxy);
        });

        // Setup error handling for Ext.Ajax
        Ext.Ajax.on("requestException",function(conn,response,opts) {
            handleError(response,opts);
        })
        var pingCount = 0;
        var pingServer = function() {
            Ext.Ajax.request({
                icingaAction: 'Ping',
                icingaModule: 'AppKit',
                isPing: true
            });
            
            
        };
    
        var handleError = function(response,proxy) {
            switch(response.status) {   
                case 200:
                    break;
                case 403:
                    break;
                case 404:
                    AppKit.AjaxErrorHandler.error_404(proxy.url);
                    break;new E
                case 401:
                    AppKit.AjaxErrorHandler.error_401(proxy.url);
                    break;
                case 500:
                    AppKit.AjaxErrorHandler.error_500(proxy.url,response);
                    break;
                default:
                    if(response.status < 400 && response.status)
                        break;
                    // check if a ping failed
                    if(proxy.isPing)
                        AppKit.AjaxErrorHandler.error_connection();
                    // check if the server is dead
                    else 
                        AppKit.AjaxErrorHandler.error_unknown(proxy.url,response); 
                    
                    break;
            }
        }
        return {
            enableErrorNotifyBox : function() {
                notifyBoxEnabled = true;
            },
            disableErrorNotifyBox : function() {
                notifyBoxEnabled = false;
            },
            enableBugTrackerReport: function() {
                bugTrackerReportEnabled = true;
            },
            disableBugTrackerReport: function() {
                bugTrackerReporEnabled = false;
            },
            error_404 : function(target) {
                trackError(_("Ressource "+target+" could not be loaded - is the url correct?"))
            },
            error_500 : function(target,response) {
                var msg = 'Internal Exception, check your logs!';
                var json = {}
                try {
                    json = Ext.decode(response.responseText)
                    msg = (json ? json.errorMessage  : response.responseText.length <400 ? response.responseText : response.responseText.substr(0,200)+"...");
                } catch(e) {
                    msg = 'Internal Exception, check your logs!';
                }
                trackError(_("The server encountered an error:<br/>")+msg,target,'XHR Request',(json ? json.isBug || false : false));
            },
            error_401 : function(target) {
                trackError(_("Access denied"));
            },
            error_connection : function(target) {
               
                Ext.Msg.alert(_("Critical error"),_("Couldn't connect to web-server."));   
                
                trackError(_("A error occured when requesting ")+target);//+" : "+error.length <200 ? error: error.substr(0,200)+"...");
            },
            error_unknown : function(target,error) {
                
                pingServer();   
                if(!error)
                    error = "Unkown error";
                    AppKit.log(error, target);
                trackError(_("A error occured when requesting ")+target);//+" : "+error.length <200 ? error: error.substr(0,200)+"...");
            }
        }

        
    };

    var setupErrorHandler = function() {
        var setHandlerFunc = function() {
            if(AppKit.getPreferences()["org.icinga.errorNotificationsEnabled"] == true) {
                AppKit.AjaxErrorHandler.enableErrorNotifyBox();
            } else {
                AppKit.AjaxErrorHandler.disableErrorNotifyBox();
            }
            if(AppKit.getPreferences()["org.icinga.bugTrackerEnabled"]  == true) {
                AppKit.BugTracker.setShowErrors(true);
                AppKit.AjaxErrorHandler.enableBugTrackerReport();
            } else {
                AppKit.BugTracker.setShowErrors(false);
                AppKit.AjaxErrorHandler.disableBugTrackerReport();
            }

        }
        if( AppKit.getPreferences()["org.icinga.errorNotificationsEnabled"] &&
                AppKit.getPreferences()["org.icinga.bugTrackerEnabled"]) {
            setHandlerFunc();
        } else {
            setupErrorHandler.defer(300);
        }
    }
    setupErrorHandler();



})();
