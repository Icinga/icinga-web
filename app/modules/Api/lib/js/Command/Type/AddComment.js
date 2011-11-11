/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command.Type');

(function () {
    "use strict";
    Icinga.Api.Command.Type.AddComment = Ext.extend(Icinga.Api.Command.Type.Abstract, {
        layout: 'form',
        border: false,
        buildForm: function() {
            
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
            }]);
            
            Icinga.Api.Command.Type.AddComment.superclass.buildForm.call(this);
        }

    });
    Icinga.Api.Command.Type.AddHostComment = Icinga.Api.Command.Type.AddComment;
    Icinga.Api.Command.Type.AddSvcComment = Icinga.Api.Command.Type.AddComment;
})();