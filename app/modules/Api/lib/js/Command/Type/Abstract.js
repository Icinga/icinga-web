/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command.Type');

(function () {
    "use strict";

/*
 * 
 * Some example code:
 * 
        var w = new Ext.Window({ title: 'test', width: 500, height: 500, renderTo: Ext.getBody() });
        var b = new Icinga.Api.Command.FormBuilder();
        var i = b.build('ADD_HOST_COMMENT', { 
            renderSubmit : true,
            targets : [{ instance : 'default', host: 'test-host' }],
            cancelHandler : function(form, action) { alert('CANCEL'); w.hide(); }
        
        });
        w.add(i);
        w.doLayout();
        w.show();
 */

    Icinga.Api.Command.Type.Abstract = Ext.extend(Ext.form.FormPanel, {

        renderSubmit: false,
        cancelHandler: null,
        padding: 5,
        
        xtypeMap: {
            date: 'datefield',
            ro: 'field',
            checkbox: 'checkbox',
            textarea: 'textarea',

            // Please leave untouched
            _default: 'field'
        },

        sourceFields: ['COMMAND_INSTANCE', 'COMMAND_HOSTGROUP', 'COMMAND_SERVICEGROUP', 'COMMAND_HOST', 'COMMAND_SERVICE', 'COMMAND_ID'],

        fieldDefaults: {
            width: 200
        },
        
        labelWidth : 160,

        constructor: function (config) {

            if (Ext.isEmpty(config.command)) {
                throw ("Need a command structural object");
            }

            Icinga.Api.Command.Type.Abstract.superclass.constructor.call(this, config);
        },

        initComponent: function () {

            if (this.renderSubmit === true) {
                this.createSubmitBar();
            }

            Icinga.Api.Command.Type.Abstract.superclass.initComponent.call(this);

            this.registerHandlers();

            var aOptions = Ext.apply({}, this.initialConfig);
            
            this.errorLabel = new Ext.form.Label({
                html :'',
                anchor: '100% 10%'
            });
            
            this.formAction = new Icinga.Api.Command.FormAction(this.getForm(), aOptions);
            
            this.on('actionfailed', this.onActionFailed, this);

            this.buildForm(this.command);
        },
        
        onActionFailed : function(form, action) {
            var json = null;
            
            try {
                json = Ext.decode(action.response.responseText);
            } catch(e) {
                json = {error: _('Unknown error, check your logs')};
            }
            
            this.errorLabel.update("<div style='float:left;width:16px;height:16px' class='icinga-icon-exclamation-red'></div><span style='color:red'>"+json.error+"</span>");
        },

        buildForm: function (o) {
        	
        	/**
        	 * Just a information for the user that he
        	 * doesn't need to take further actions
        	 */
        	if (this.countRealFields() === 0) {
        		this.add({
        			xtype : 'panel',
        			border: false,
        			html : _('No more fields required. Just press "Send" to commit.')
        		});
        	}
        	
        	if (this.errorLabel) {
        		this.add(this.errorLabel);
        	}
        	
        	this.doLayout();
        },

        registerHandlers: function () {
            this.form.on('actioncomplete', function (form, action) {
                this.disable();
            }, this);
        },

        createSubmitBar: function () {

            this.buttons = [{
                text: _('Send'),
                iconCls: 'icinga-action-icon-ok',
                handler: function (b, e) {
                    var form = this.getForm();
                    form.doAction(this.formAction);
                },
                scope: this
            }];

            if (this.cancelHandler !== null) {
                this.buttons.push({
                    text: _('Cancel'),
                    iconCls: 'icinga-action-icon-cancel',
                    handler: function (b, e) {
                        this.cancelHandler(this.form, this.formAction);
                    },
                    scope: this
                });
            }
        },

        getFieldLabel: function (fieldLabel) {
            return fieldLabel.charAt(0).toUpperCase() + fieldLabel.substr(1).toLowerCase();
        },

        getMappedXtype: function (type) {
            if (type in this.xtypeMap) {
                return this.xtypeMap[type];
            } else {
                return this.xtypeMap._default;
            }
        },

        getExtFieldDefinition: function (fieldParams) {
            var oDefault = {
                name: fieldParams.alias,
                allowBlank: !fieldParams.required,
                xtype: this.getMappedXtype(fieldParams.type),
                fieldLabel: this.getFieldLabel(fieldParams.alias)
            };

            Ext.applyIf(oDefault, this.fieldDefaults);

            return oDefault;
        },

        isSourceField: function (fieldName) {
            if (this.sourceFields.indexOf(fieldName) > -1) {
                return true;
            }

            return false;
        },
        
        countRealFields : function() {
        	var c = 0;
        	Ext.iterate(this.command.parameters, function (key, value) {
        		if (this.isSourceField(key) === false) {
        			c++;
        		}
        	}, this);
        	return c;
        }
    });
})();