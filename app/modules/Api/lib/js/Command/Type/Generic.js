/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command.Type');

(function () {
    "use strict";

    Icinga.Api.Command.Type.Generic = Ext.extend(Icinga.Api.Command.Type.Abstract, {

        buildForm: function (o) {
            Ext.iterate(o.parameters, function (key, value) {
                if (this.isSourceField(key) === false) {
                    var field = this.getFieldByName(key, value);
                    this.add(field);
                }
            }, this);
            
            Icinga.Api.Command.Type.Generic.superclass.buildForm.call(this, o);
        },

        getFieldByName: function (fieldName, fieldParams) {

            var oDef = this.getExtFieldDefinition(fieldParams);

            this.changeFieldAttributes(oDef, fieldName, fieldParams);

            var field = Ext.ComponentMgr.create(oDef, fieldName);

            return field;
        },

        changeFieldAttributes: function (oDef, fieldName, fieldParams) {
            if (fieldName === 'COMMAND_AUTHOR') {
                oDef.value = AppKit.getPreferences().author_name;
            }

            if (fieldParams.type === 'ro') {
                oDef.readOnly = true;
            }

            return oDef;
        }
    });
})();