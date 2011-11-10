/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command.Type');

(function () {
    "use strict";
    Icinga.Api.Command.Type.AcknowledgeProblem = Ext.extend(Icinga.Api.Command.Type.Abstract, {
        layout: 'form',
        buildForm: function() {
            var errorLabel = new Ext.form.Label({
                
                html :'',
                anchor: '100% 10%'
            })
            this.add([
            {
                xtype: 'checkbox',
                boxLabel: _('Keep acknowledged until object is up again'),
                name: 'sticky',
                getValue: function() {
                    return this.checked ? 0 : 2;
                },
                anchor: '100%'
            },{
                xtype: 'checkbox',
                boxLabel: _('Notify contacts about acknowledgement'),
                name: 'notify',
                getValue: function() {
                    return this.checked ? 0 : 1;
                },
                anchor: '100%'
            },{
                xtype: 'checkbox',
                boxLabel: _('Keep acknowledgedement persistent (i.e. stays after icinga restart)'),
                name: 'persistent',
                getValue: function() {
                    return this.checked ? 0 : 1;
                },
                anchor: '100%'
            }, {
                xtype: 'hidden',
                name: 'author',
                value: AppKit.getPrefVal("author_name")
            },{
                xtype: 'textarea',
                fieldLabel: _('Comment'),
                name: 'comment',
                anchor: '100% 60%',
                height: 300
            },errorLabel
            ]);
            this.formAction.failure = function(rawResponse) {
                var json = {error: _('Unknown error, check your logs')};
                try {
                    json = Ext.decode(rawResponse.responseText);
                } catch(e) {
                    json = {error: _('Unknown error, check your logs')};
                }
                errorLabel.update("<div style='float:left;width:16px;height:16px' class='icinga-icon-exclamation-red'></div><span style='color:red'>"+json.error+"</span>")
                this.failureType = Ext.form.Action.SERVER_INVALID;
                this.form.afterAction(this, false);
            }
        }

    });
    Icinga.Api.Command.Type.AcknowledgeHostProblem = Icinga.Api.Command.Type.AcknowledgeProblem;
    Icinga.Api.Command.Type.AcknowledgeSvcProblem = Icinga.Api.Command.Type.AcknowledgeProblem;
})();