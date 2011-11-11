/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command.Type');

(function () {
    "use strict";
    Icinga.Api.Command.Type.AcknowledgeProblem = Ext.extend(Icinga.Api.Command.Type.Abstract, {
        layout: 'form',
        buildForm: function() {
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
            }]);
            
            Icinga.Api.Command.Type.AcknowledgeProblem.superclass.buildForm.call(this);
        }

    });
    
    Icinga.Api.Command.Type.AcknowledgeHostProblem = Icinga.Api.Command.Type.AcknowledgeProblem;
    Icinga.Api.Command.Type.AcknowledgeSvcProblem = Icinga.Api.Command.Type.AcknowledgeProblem;
})();