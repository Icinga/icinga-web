/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Comment');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Comment.CreateForm = Ext.extend(Ext.Panel, {

        title: _('Add new comment'),
        autoScroll : true,
        type: null,
        objectName: null,
        objectInstance: null,
        objectId: null,
        record: null,
        command: null,
        target: null,
        form: null,
        formBuilder: null,

        constructor: function (config) {

            if (Ext.isEmpty(config.type)) {
                throw ("config.type is mandatory (host, service)");
            }

            Icinga.Cronks.Tackle.Comment.CreateForm.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.Comment.CreateForm.superclass.initComponent.call(this);

            this.formBuilder = new Icinga.Api.Command.FormBuilder();
        },

        rebuildForm: function () {
            var command = 'ADD_' + (this.type.toUpperCase() == 'SERVICE' ? 'SVC' : this.type.toUpperCase())+ '_COMMENT';

            // Leave if we do not need to rebuild
            if (this.command === command) {
                return;
            }

            this.command = command;

            this.target = {};
            this.target.instance = this.objectInstance;
            this.target.host = this.record.get('HOST_NAME');
            this.target[this.type] = this.objectName;

            this.removeAll();

            var formReset = function () {
                    this.form.form.reset();
                    this.form.enable();
            };

            var cancelHandler = function () {
                    this.form.form.reset();
                    this.collapse();
            };
            AppKit.log(this.target,this.form);
            this.form = this.formBuilder.build(this.command, {
                renderSubmit: true,
                targets: [this.target],
                cancelHandler: cancelHandler.createDelegate(this)
            });

            this.form.form.on('actioncomplete', formReset.createDelegate(this));

            this.add(this.form);

            this.doLayout();
        },

        setObjectData: function (o) {
            Ext.copyTo(this, o, ['objectName', 'objectInstance', 'objectId','record']);
            this.rebuildForm();
        }
    });

})();