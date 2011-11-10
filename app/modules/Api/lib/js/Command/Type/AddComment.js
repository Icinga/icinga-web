/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command.Type');

(function () {
    "use strict";
    Icinga.Api.Command.Type.AddComment = Ext.extend(Icinga.Api.Command.Type.Abstract, {
        layout: 'form',
        border: false,
        buildForm: function() {
            var errorLabel = new Ext.form.Label({
                
                html :'',
                anchor: '100% 10%'
            })
            this.add([{
                xtype: 'checkbox',
                boxLabel: _('Create persistent comment (i.e. stays after icinga restart)'),
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
                allowBlank : false,
                name: 'comment',
                anchor: '100% 80%'
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
    Icinga.Api.Command.Type.AddHostComment = Icinga.Api.Command.Type.AddComment;
    Icinga.Api.Command.Type.AddSvcComment = Icinga.Api.Command.Type.AddComment;
})();