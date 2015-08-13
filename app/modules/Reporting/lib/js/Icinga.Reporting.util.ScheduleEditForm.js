// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
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

Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ScheduleEditForm = Ext.extend(Ext.form.FormPanel, {

    constructor : function(config) {

        config = Ext.apply(config || {}, {
            border : false,

            labelAlign : 'top',
            msgTarget : 'side',

            bbar : [{
                text : _('Save'),
                iconCls : 'icinga-icon-accept',
                handler : this.processFormSave,
                scope : this
            }, '-', {
                text : _('Cancel'),
                iconCls : 'icinga-icon-cancel',
                handler : this.processFormCancel,
                scope : this
            }]
        });

        Icinga.Reporting.util.ScheduleEditForm.superclass.constructor.call(this, config);
    },

    initComponent : function() {

        Icinga.Reporting.util.ScheduleEditForm.superclass.initComponent.call(this);

        this.formTabs = new Ext.TabPanel({
            height : 600,
            border : false,
            activeTab : 0,

            layoutConfig : {
                deferredRender : false
            },

            forceLayout : true,

            defaults:{
                bodyStyle:'padding:10px',
                layout : 'form',
                forceLayout : true
            },
            items : [{
                iconCls : 'icinga-cronk-icon-1',
                title : _('Basics'),
                autoScroll : true,
                height : 560,
                defaults : {
                    width : 300
                },
                items : [{
                    xtype : 'textfield',
                    fieldLabel : _('Job name'),
                    name : 'label'
                }, {
                    xtype : 'textarea',
                    fieldLabel : _('Description'),
                    name : 'description'
                }, {
                    xtype : 'textfield',
                    fieldLabel : _('Report URI'),
                    name : 'reportUnitURI',
                    readOnly : true
                }, {
                    xtype : 'textfield',
                    fieldLabel : _('Job ID'),
                    name : 'id',
                    readOnly : true,
                    value : 0
                }, {
                    xtype : 'textfield',
                    fieldLabel : _('Job Version'),
                    name : 'version',
                    readOnly : true,
                    value : 0
                }]
            }, {
                iconCls : 'icinga-cronk-icon-2',
                title : _('Recurrence'),
                autoScroll : true,
                items : [{
                    xtype : 'radiogroup',
                    fieldLabel : _('Type'),
                    name : 'trigger',
                    anchor : '50%',
                    listeners : {
                        change : this.processTriggerTypeChange.createDelegate(this)
                    },
                    items : [{
                        xtype : 'radio',
                        boxLabel : _('None'),
                        name : 'trigger',
                        inputValue : 'recurrence-none',
                        checked : true
                    }, {
                        xtype : 'radio',
                        boxLabel : _('Simple'),
                        name : 'trigger',
                        inputValue : 'recurrence-simple'
                    }, {
                        xtype : 'radio',
                        boxLabel : _('Calendar'),
                        name : 'trigger',
                        inputValue : 'recurrence-calendar'
                    }]
                }, {
                    xtype : 'fieldset',
                    title : _('Simple'),
                    id : this.getId() + '-recurrence-simple',
                    hidden : false,
                    items : [{
                        xtype : 'container',
                        height : 80,
                        layout : {
                            type : 'vbox',
                            defaultMargins : ' 0 10 0 0'
                        },
                        items : [{
                            xtype : 'radio',
                            name : 'simpleTrigger.startType',
                            boxLabel : _('Run immediately'),
                            checked : true,
                            inputValue : 1
                        }, {
                            xtype : 'container',
                            width : 350,
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 10 0 0 0'
                            },
                            items : [{
                                xtype : 'radio',
                                name : 'simpleTrigger.startType',
                                boxLabel : _('On'),
                                inputValue : 2,
                                listeners : {
                                    check : this.processStartTimeToggle.createDelegate(this, ['simpleTrigger.startDate'], true)
                                }
                            }, {
                                xtype : 'datefield',
                                name : 'simpleTrigger.startDate',
                                format : 'c',
                                disabled : true,
                                width : 300
                            }]
                        }]
                    }, {
                        xtype : 'container',
                        layout : {
                            type : 'hbox',
                            defaultMargins : ' 10 0 0 0' // BUG: Prefix with space to get work
                        },
                        fieldLabel : 'Repeat every',
                        items : [{
                            xtype : 'textfield',
                            width : 50,
                            value : 1,
                            name : 'simpleTrigger.recurrenceInterval'
                        }, {
                            xtype : 'combo',
                            typeAhead : true,
                            triggerAction : 'all',
                            width: 100,

                            mode : 'local',
                            store : new Ext.data.ArrayStore({
                                id : 0,
                                fields : [
                                    'interval',
                                    'label'
                                ],
                                data : [
                                    ['MINUTE', _('minutes')],
                                    ['HOUR', _('hours')],
                                    ['DAY', _('days')],
                                    ['WEEK', _('weeks')]
                                ]
                            }),
                            value : 'DAY',
                            valueField : 'interval',
                            displayField : 'label',
                            hiddenName : 'simpleTrigger.recurrenceIntervalUnit',
                            name : 'simpleTrigger.recurrenceIntervalUnit'
                        }]
                    }, {
                        xtype : 'container',
                        layout : {
                            type : 'vbox',
                            defaultMargins : ' 0 10 0 0'
                        },
                        height : 150,
                        items : [{
                            xtype : 'container',
                            width: 300,
                            height : 25,
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 10 0 0 0'
                            },
                            items : [{
                                xtype : 'radio',
                                boxLabel : _('Indefinitely'),
                                name : 'simpleTrigger.recurrenceType',
                                inputValue : 1,
                                checked : true
                            }]
                        }, {
                            xtype : 'container',
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 10 0 0 0'
                            },
                            width : 350,
                            height : 25,
                            items : [{
                                xtype : 'radio',
                                boxLabel : _('Times'),
                                name : 'simpleTrigger.recurrenceType',
                                inputValue : 2,
                                width : 60,
                                listeners : {
                                    check : this.processRecurrenceChange.createDelegate(this, ['simpleTrigger.occurrenceCount'], true)
                                }
                            }, {
                                xtype : 'textfield',
                                name : 'simpleTrigger.occurrenceCount',
                                disabled : true,
                                width: 200
                            }]
                        }, {
                            xtype : 'container',
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 10 0 0 0' // BUG: Prefix with space to get work
                            },
                            width : 350,
                            height : 25,
                            items : [{
                                xtype : 'radio',
                                boxLabel : _('Until'),
                                name : 'simpleTrigger.recurrenceType',
                                inputValue : 3,
                                width : 60,
                                listeners : {
                                    check : this.processRecurrenceChange.createDelegate(this, ['simpleTrigger.endDate'], true)
                                }
                            }, {
                                xtype : 'datefield',
                                name : 'simpleTrigger.endDate',
                                format : 'c',
                                disabled : true,
                                width: 200
                            }]
                        }]
                    }]
                }, {
                    xtype : 'fieldset',
                    title : _('Calendar'),
                    id : this.getId() + '-recurrence-calendar',
                    height: 500,
                    hidden : false,
                    items : [{
                        xtype : 'container',
                        height : 80,
                        layout : {
                            type : 'vbox',
                            defaultMargins : ' 0 10 0 0'
                        },
                        items : [{
                            xtype : 'radio',
                            name : 'calendarTrigger.startType',
                            boxLabel : _('Run immediately'),
                            checked : true,
                            inputValue : 1
                        }, {
                            xtype : 'container',
                            width : 350,
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 10 0 0 0'
                            },
                            items : [{
                                xtype : 'radio',
                                name : 'calendarTrigger.startType',
                                boxLabel : _('On'),
                                inputValue : 2,
                                listeners : {
                                    check : this.processStartTimeToggle.createDelegate(this, ['calendarTrigger.startDate'], true)
                                }
                            }, {
                                xtype : 'datefield',
                                name : 'calendarTrigger.startDate',
                                format : 'c',
                                disabled : true,
                                width : 300
                            }]
                        }]
                    }, {
                        xtype : 'container',
                        height : 200,
                        autoScroll : true,
                        defaults : {
                            flex : 0
                        },
                        layout : {
                            type : 'vbox'
                        },
                        items: [{
                            border : false,
                            xtype : 'container',
                            layout : {
                                type : 'column'
                            },
                            defaults : {
                                border : false
                            },
                            height : 200,
                            items : [{
                                columnWidth : '200px',
                                width: 200,
                                height : 200,
                                layout: {
                                    type : 'vbox',
                                    defaultMargins : ' 0 5 0 0'
                                },
                                items: [{
                                    xtype : 'label',
                                    text : _('Months')
                                }, {
                                    xtype : 'radio',
                                    boxLabel : _('Every month'),
                                    name : 'calendarTrigger.monthsType',
                                    inputValue : 1
                                }, {
                                    xtype : 'radio',
                                    boxLabel : _('Selected months'),
                                    name : 'calendarTrigger.monthsType',
                                    inputValue : 2
                                }, new Icinga.Reporting.widget.MonthPicker({
                                    name : 'calendarTrigger.months',
                                    height : 80
                                })]
                            }, {
                                columnWidth : '200px',
                                width : 200,
                                height: 200,
                                layout : {
                                    type : 'vbox',
                                    defaultMargins : ' 0 5 0 0'
                                },
                                items: [{
                                    xtype : 'label',
                                    text : _('Days')
                                }, {
                                    xtype : 'radio',
                                    boxLabel : _('Every day'),
                                    name : 'calendarTrigger.daysType',
                                    inputValue : 'ALL'
                                }, {
                                    xtype : 'radio',
                                    boxLabel : _('Week days'),
                                    name : 'calendarTrigger.daysType',
                                    inputValue : 'WEEK'
                                }, new Icinga.Reporting.widget.WeekDayPicker({
                                    name : 'calendarTrigger.weekDays',
                                    height : 80
                                }), {
                                    xtype : 'radio',
                                    boxLabel : _('Month days'),
                                    name : 'calendarTrigger.daysType',
                                    inputValue : 'MONTH'
                                }, {
                                    xtype : 'textfield',
                                    name : 'calendarTrigger.monthDays'
                                }]
                            }]
                        }]
                    }, {
                        xtype : 'container',
                        layout : {
                            type : 'vbox',
                            defaultMargins : ' 0 5 0 0'
                        },
                        defaults : {
                            width : 300
                        },
                        height: 300,
                        items : [{
                            xtype : 'label',
                            text : _('Times')

                        }, {
                            xtype : 'container',
                            height : 25,
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 0 0 10 0'
                            },
                            items : [{
                                xtype : 'textfield',
                                name : 'calendarTrigger.hours'
                            }, {
                                xtype : 'label',
                                text : _('Hours')
                            }]
                        }, {
                            xtype : 'label',
                            height : 25,
                            text : _('Hint: Enter 24-hour times like 9,12,15 or ranges like 9-12,1-17')
                        }, {
                            xtype : 'container',
                            height : 25,
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 0 0 10 0'
                            },
                            items : [{
                                xtype : 'textfield',
                                name : 'calendarTrigger.minutes'
                            }, {
                                xtype : 'label',
                                text : _('Minutes')
                            }]
                        }, {
                            xtype : 'label',
                            height : 25,
                            text : _('Hint: Enter 0,15,30,45 to run every 1/2 hour')
                        }, {
                            xtype : 'container',
                            height : 25,
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 10 0 0 0'
                            },
                            items : [{
                                xtype : 'label',
                                text : _('Recur until')
                            }, {
                                xtype :'datefield',
                                name : 'calendarTrigger.endDate',
                                format : 'c',
                                width : 200
                            }]
                        }]
                    }]
                }]
            }, {
                iconCls : 'icinga-cronk-icon-3',
                title : _('Parameters'),
                autoScroll : true,
                layout : {
                    type : 'auto'
                },
                items : [{
                    xtype : 'container',
                    items : [{
                        xtype : 'fieldset',
                        title : _('Parameterise report'),
                        ref: '../../../parameterFieldset'
                    }, {
                        xtype: 'panel',
                        layout : 'fit',
                        ref: '../../../parameterDisplay',
                        hidden: true,
                        html : String.format(
                            '<h4>{0}</h4><i>{1}</i>',
                            _('No more parameters'),
                            _('Nothing else needed here, just press "Run" or "Preview" to proceed')
                        ),
                        border : false,
                        cls : 'simple-content-box'
                    }]
                }]
            }, {
                iconCls : 'icinga-cronk-icon-4',
                title : _('Output'),
                autoScroll : true,
                layout : {
                    type : 'auto'
                },
                items : [{
                    xtype : 'container',
                    layout : {
                        type : 'form'
                    },
                    items : [{
                        xtype : 'fieldset',
                        title : _('Output identification'),
                        defaults : {
                            width : 400
                        },
                        items : [{
                            xtype : 'textfield',
                            fieldLabel : _('Base output filename'),
                            name : 'baseOutputFilename'
                        }, {
                            xtype : 'textarea',
                            fieldLabel : _('Output description'),
                            name : 'repositoryDestination.outputDescription'
                        }]
                    }, {
                        xtype : 'fieldset',
                        title : _('Output location'),
                        defaults : {
                            width : 400
                        },
                        items : [{
                            xtype : 'textfield',
                            fieldLabel : _('The file will be added to'),
                            name : 'repositoryDestination.folderURI'
                        }, {
                            xtype : 'container',
                            layout : {
                                type : 'hbox',
                                defaultMargins : ' 10 0 0 0'
                            },
                            items : [{
                                xtype : 'checkbox',
                                boxLabel : _('Sequential File Names'),
                                inputValue : true,
                                name : 'repositoryDestination.sequentialFilenames'
                            }, {
                                xtype : 'textfield',
                                name : 'repositoryDestination.timestampPattern'
                            }, {
                                xtype : 'label',
                                text : '(' + _('Timestamp pattern') + ')'
                            }]
                        }, {
                            xtype : 'checkbox',
                            boxLabel : _('Overwrite files'),
                            name : 'repositoryDestination.overwriteFiles',
                            inputValue : true
                        }]
                    }, {
                        xtype : 'fieldset',
                        title : _('Output format'),
                        defaults : {
                            width : 400
                        },
                        items : [{
                            xtype : 'checkboxgroup',
                            name : 'outputFormats',
                            columns : 6,
                            items : [
                                { name : 'outputFormats.PDF', boxLabel : _('PDF'), inputValue : 1 },
                                { name : 'outputFormats.HTML', boxLabel : _('HTML'), inputValue : 2 },
                                { name : 'outputFormats.XLS', boxLabel : _('Excel'), inputValue : 3 },
                                { name : 'outputFormats.CSV', boxLabel : _('CSV'), inputValue :4 },
                                { name : 'outputFormats.DOC', boxLabel : _('DOCX'), inputValue : 5 },
                                { name : 'outputFormats.RTF', boxLabel : _('RTF'), inputValue : 6 },
                                { name : 'outputFormats.ODT', boxLabel : _('ODT'), inputValue : 7 },
                                { name : 'outputFormats.ODS', boxLabel : _('ODS'), inputValue : 8 },
                                { name : 'outputFormats.XSLX', boxLabel : _('XSLX'), inputValue : 9 }
                            ]
                        }]
                    }, {
                        xtype : 'fieldset',
                        title : _('Email notification'),
                        defaults : {
                            width : 400
                        },
                        items :[{
                            xtype : 'textarea',
                            fieldLabel : _('To'),
                            name : 'mailNotification.toAddresses'
                        }, {
                            xtype : 'label',
                            text : ('Hint: use commas to separate addresses')
                        }, {
                            xtype : 'textfield',
                            fieldLabel : _('Subject'),
                            name : 'mailNotification.subject'
                        }, {
                            xtype : 'textarea',
                            fieldLabel : _('Message'),
                            name : 'mailNotification.messageText'
                        }, {
                            xtype : 'checkbox',
                            boxLabel : _('Attach files'),
                            name : 'mailNotification.resultSendType',
                            inputValue : true
                        }, {
                            xtype : 'checkbox',
                            boxLabel : _('Skip empty reports'),
                            name : 'mailNotification.skipEmptyReports',
                            inputValue : true
                        }]
                    }]
                }]
            }]
        });

        /*
         * Dirty hack for form processing:
         * Items are rendered only if the tab has been activated. To initialize
         * the initial state of the form do this on first show
         */
        (function() {
            this.formTabs.getItem(1).on('show', function() {
                if (Ext.isEmpty(this.job_id)) {
                    this.getForm().findField('trigger').setValueForItem('recurrence-simple');
                }
            }, this, { single : true });
        }).defer(1000, this);

        /*
         * Need to prerender all items in tab panel. Because
         * the form is not ready we don't do this.
         * @todo Check why deferred rendering is not working in card layout
         */
        this.formTabs.on('afterrender', function(component) {
            component.findBy(function(item, two) {
                item.show();
                if (['panel'].indexOf(item.getXType()) >= 0) {
                    item.doLayout();
                }
            }, this);

            this.collapse(false);

        }, this, { single : true, delay : 400 });
        /*
         * ----------------------------------------------------------------
         */

        this.on('afterrender', function() {
            this.formTabs.setHeight(this.parentCmp.getInnerHeight()-30);
        }, this, { single : true })
        this.add(this.formTabs);

        this.doLayout();

    },

    processStartTimeToggle : function(checkboxChecked, checked, fieldName) {
        var field = this.getForm().findField(fieldName);
        if (field) {
            field.setDisabled(!checked);
        }
    },

    processTriggerTypeChange : function(radioGroup, radioChecked) {
        var h = ['recurrence-simple', 'recurrence-calendar'];
        i = h.length;
        while (i--) {
            var cmp = Ext.getCmp(String.format('{0}-{1}', this.getId(), h[i]));
            if (!cmp) {
                continue;
            }

            if (radioChecked.inputValue == h[i]) {
                cmp.show();
            }
            else {
                cmp.hide();
            }
        }


    },

    processRecurrenceChange : function(checkBox, checked, field) {
        var field = this.getForm().findField(field);
        if (field) {
            field.setDisabled(!checked);
        }
    },

    processFormSave : function() {

        var dataTool = new Icinga.Reporting.util.JobFormValues({
            form : this.getForm()
        });

        var params = {
            job_data : Ext.encode(dataTool.createJsonStructure()),
            uri : this.report_uri
        }

        this.parentCmp.showMask();

        Ext.Ajax.request({
            url : this.scheduler_edit_url,
            params : params,
            success : function(response, opts) {
                try {
                    var re = Ext.decode(response.responseText);
                    if (re.success === true) {
                        this.cancelEdit();
                    } else {
                        AppKit.notifyMessage(_('Error'), _(String.format(_('Could not save: {0}'), re.error)));
                    }
                } catch (e) {
                    AppKit.notifyMessage(_('Error'), _(String.format(_('Could not parse response: {0}'), e)));
                    this.parentCmp.hideMask();
                }

                this.parentCmp.hideMask();
            },
            failure : function(response, opts) {
                this.parentCmp.hideMask();
            },
            scope : this
        })
    },

    processFormCancel : function() {
        this.cancelEdit();
    },

    resetForm : function() {
        this.report_uri = null;
        this.job_id = null;

        try {
            this.getForm().reset();
        } catch (e) {
            // DO NOTHING
        }
    },

    cancelEdit : function() {
        this.resetForm();
        this.collapse(true);
        this.parentCmp.reloadTaskList();
    },

    startEdit : function(report_uri, job_id) {

        this.resetForm();

        this.report_uri = report_uri;

        var params = {
            uri : report_uri
        };

        if (job_id) {
            params.job = job_id
            this.job_id = job_id;
        }

        if (params.uri) {
            this.formTabs.setActiveTab(0);

            Ext.Ajax.request({
                url : this.scheduler_get_url,
                params : params,
                success : function(response, options) {
//                  try {
                        var data = Ext.util.JSON.decode(response.responseText);
                        this.applyFormData.defer(10, this, [data]);
//                  } catch (e) {
//                      AppKit.notifyMessage(_('Error'), _(String.format(_('Could not parse response: {0}'), e)));
//                  }
                },
                scope : this
            })
            this.expand(true);
        }
    },

    applyFormData : function(data) {
        this.createReportParametersForm(data.inputControls);

        if (!Ext.isEmpty(data.job)) {
            var dataTool = new Icinga.Reporting.util.JobFormValues({
                form : this.getForm(),
                data : data.job
            });

            dataTool.applyFormValues();
        } else {
            this.getForm().findField('reportUnitURI').setValue(this.report_uri);
        }
    },

    /**
     * Build the input widgets for report parameters
     * @param {Object} controls
     */
    createReportParametersForm : function(controls) {
        var fieldset = this.parameterFieldset;
        var display = this.parameterDisplay;

        if (fieldset && display) {
            var builder = new Icinga.Reporting.util.InputControlBuilder({
                target : fieldset,
                controlStruct : controls,
                removeAll : true,
                namePrefix : 'parameters.'
            });

            if (builder.hasControls()) {
                fieldset.show();
                display.hide();
                builder.applyToTarget();
            } else {
                fieldset.removeAll(true); // Drop all old items
                fieldset.hide();
                display.show();
            }
        } else {
            throw ("Could not get elements: Icinga.Reporting.util.ScheduleEditForm/createReportParametersForm()");
        }
    }
});
