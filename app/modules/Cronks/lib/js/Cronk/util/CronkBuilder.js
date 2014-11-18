// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns("Cronk.util.CronkBuilder");


(function() {
    
    "use strict";
    
    Cronk.util.CronkBuilder = Ext.extend(Ext.Window, {
        title: _('Save custom Cronk'),
        modal: true,
        closeAction: 'hide',
        
        minWidth: 800,
        width: 880,
        minHeight: 400,
        height: 400,
        
        // Contains only one direct child item, a form
        layout: 'fit',

        constructor : function(config) {
            this.addEvents({
                writeSuccess : true
            });
            
            Cronk.util.CronkBuilder.superclass.constructor.call(this, config);
        },
        
        initComponent : function() {
           

            this._buildBars();
            
            Cronk.util.CronkBuilder.superclass.initComponent.call(this);
            
            this.paramGrid = this._paramGrid();
            
            this.iconCombo = this._iconCombo();
            
            this.formPanel = this._buildForm();
            
            this.action = new Cronk.form.action.CronkBuilderCustom(this.formPanel.getForm(), {
                params: { xaction: 'write' },
                url: AppKit.c.path + '/modules/cronks/provider/cronks/',
                success: function() {
                    this.hide();
                    this.fireEvent('writeSuccess');
                    AppKit.notifyMessage(_('CronkBuilder'), String.format(_('Cronk "{0}" successfully written to database!'), this.formPanel.getForm().findField('name').getValue()));
                },
                failure: function(form, action) {
                    if (action.failureType === Ext.form.Action.CLIENT_INVALID) {
                        AppKit.notifyMessage(_('Error'), _('Please fill out required fields (marked with an exclamation mark)'));
                    }
                },
                scope: this
            }, this.paramGrid);

            this.add(this.formPanel);

            // Prevent active from being edited by someone because
            // we need to get some data 
            this.on("afterrender", function() {
                this.loadMask = new Ext.LoadMask(this.getEl(), {
                    store: this.categories,
                    msg: _('Loading categories ...')
                });
            }, this);
            
            // Hide the fieldsets after rendering for
            // calculating sizes right
            this.on("beforeshow", function(c) {
                var checkItem = Ext.getCmp('cb-checkitem-expert-mode');
                
                if (checkItem.checked === true) {
                    this.showExpertMode(true);
                }
                else {
                    this.showExpertMode(false);
                }

                // If someone changes categories behind the
                // scenes
                this.categories.reload();

            }, this);

            
        },
        
        _buildBars : function() {
            
            var CB = this;
            
            this.buttons = [{
                text: _('Save'),
                iconCls: 'icinga-icon-accept',
                handler: function(b, e) {
                    this.formPanel.getForm().doAction(this.action);
                },
                scope: this
            }, {
                text: _('Cancel'),
                iconCls: 'icinga-icon-cross',
                handler: function(b, e) {
                    CB.hide();
                }
            }];
            
            this.tbar = [{
                text: _('Settings'),
                iconCls: 'icinga-icon-wrench-screwdriver',
                menu: new Ext.menu.Menu({
                    items: [{
                        id: 'cb-checkitem-expert-mode',
                        text: _('Expert mode'),
                        checked: false,
                        checkHandler: function(checkItem, checked) {
                            if (checked === true) {
                                this.showExpertMode(true);
                            }
                            else {
                                this.showExpertMode(false);
                            }
                        },
                        scope: CB
                    }]
                })
            }];
        },
        
        _iconCombo: function() {
            var iconStore = new Ext.data.JsonStore({
                autoDestroy: true,
                url: AppKit.c.path + '/modules/appkit/provider/icons',
                baseParams: { path: 'cronks' },
                fields: ['web_path', 'name', 'short'],
                root: 'rows'
            });
            
            iconStore.load();
            
            var iconTpl = new Ext.XTemplate(
                '<tpl for="."><div class="x-icinga-icon-search-item" style="background-image: url({web_path});">',
                    '<span>{name}</span>',
                '</div></tpl>'
            );
            
            var combo = new Ext.form.ComboBox({
                xtype: 'combo',
                store: iconStore,
                displayField: 'name',
                typeAhead: false,
                loadingText: _('Searching ...'),
                hideTrigger: false,
                tpl: iconTpl,
                itemSelector: 'div.x-icinga-icon-search-item',
                triggerAction: 'all',
                valueField: 'short',
                width: 200,
                height: 40,
                allowBlank: false,
                name: 'image',
                fieldLabel: _('Image')
            });
            
            combo.on('select', function() {
                this.refreshIconPreview();
            }, this);
            
            return combo;
        },
        
        _paramGrid : function() {
            return new Ext.grid.PropertyGrid({
                id: 'cronkbuilder-param-properties',
                height: 210,
                viewConfig : {
                    forceFit: true,
                    scrollOffset: 2
                },
                bbar: {
                    width: 200,
                    items: [{
                        iconCls: 'icinga-icon-add',
                        text: _('Add'),
                        handler: function(b, e) {
                            Ext.MessageBox.prompt(_('Add'), _('Add new parameter to properties'), function(btn, text) {
                                if (!Ext.isEmpty(text)) {
                                    var rec = new Ext.grid.PropertyRecord({
                                        name: text,
                                        value: null
                                    }, text);
                                    this.paramGrid.store.addSorted(rec);
                                }
                            }, this);
                        },
                        scope: this
                    }, {
                        iconCls: 'icinga-icon-delete',
                        text: _('Remove'),
                        handler: function(b, e) {
                            var sel = this.paramGrid.getSelectionModel().selection;
                            try {
                                this.paramGrid.removeProperty(sel.record.id);
                            } catch (e) {
                                AppKit.notifyMessage(_('Error'), _('No selection was made!'));
                            }
                        },
                        scope: this
                    }]
                }
            });
        },
        
        _roleCombo: function() {
            
            this.groups = new Ext.data.JsonStore({
                autoDestroy: true,
                url: AppKit.c.path + '/modules/appkit/provider/groups?oldBehaviour=0',
                fields : [{
                    name : 'id'
                }, {
                    name : 'name'
                }],
                idProperty : 'id',
                root : 'roles',
                totalProperty : 'totalCount',
                successProperty : 'success'
            });
            
            this.groups.load();
            
            this.roleCheckbox = Ext.create({
                xtype: 'checkbox',
                name: 'share',
                fieldLabel: 'Make your cronk available for others',
                msgTarget: 'side',
                handler: function(c, checked) {
                    var field = this.formPanel.getForm().findField('roles');
                    
                    if (checked === true) {
                        field.enable();
                    }
                    else {
                        field.disable();
                    }
                },
                scope: this
            });
            
            this.roleSelect = Ext.create({
                xtype: 'multiselect',
                name: 'roles',
                style: { overflow: 'hidden' },
                width: 200,
                height: 100,
                fieldLabel: _('Roles'),
                store: this.groups,
                valueField: 'id',
                displayField: 'name',
                disabled: true,
                msgTarget: 'side'
            });
            
            this.roleLabel = Ext.create({
                xtype: 'panel',
                border: false,
                html: _('Sorry, no roles to share you cronk!'),
                hidden: true
            });
            
            this.groups.on('load', function(store, records, options) {
                if (store.getCount() <= 0) {
                    this.roleCheckbox.hide();
                    this.roleSelect.hide();
                    this.roleLabel.show();
                } else {
                    this.roleCheckbox.show();
                    this.roleSelect.show();
                    this.roleLabel.hide();
                }
            }, this);
            
            return [
                this.roleCheckbox,
                this.roleSelect,
                this.roleLabel
            ];
        },
        
        _buildForm: function() {
            this.categories = new Ext.data.JsonStore({
                autoDestroy: true,
                url: AppKit.c.path + '/modules/cronks/provider/cronks/categories',
                baseParams: { all : 1 },
                writer: new Ext.data.JsonWriter({
                    encode: true,
                    writeAllFields: false
                })
            });
            
            this.categories.load();
            
            var formPanel = new Ext.form.FormPanel({
                layout: 'border',
                padding: '5px 0 5px 0',
                border: false,
                
                defaults: {
                    border: false
                },
                
                items: [{
                    padding: '5px',
                    layout: 'form',
                    
                    region: 'center',
                    items: [{
                        xtype: 'fieldset',
                        title: _('Meta'),
                        
                        defaults: {
                            width: 220,
                            msgTarget: 'side'
                        },
                        
                        items: [{
                            xtype: 'textfield',
                            name: 'name',
                            fieldLabel: _('Name'),
                            allowBlank: false
                        }, {
                            xtype: 'textfield',
                            name: 'description',
                            fieldLabel: _('Description'),
                            allowBlank: false
                        }, {
                            xtype: 'textfield',
                            name: 'cid',
                            fieldLabel: _('Cronk Id'),
                            readOnly: true,
                            allowBlank: false,
                            style: {
                                background: '#CF6'
                            }
                        }
                        /*, {
                            xtype: 'checkbox',
                            name: 'hide',
                            fieldLabel: _('Hidden')
                        }
                        */
                        ]
                    }, {
                        xtype: 'fieldset',
                        title: _('Categories'),
                        height: 180,
                        items: [{
                            xtype: 'multiselect',
                            name: 'categories',
                            style: { overflow: 'hidden' },
                            fieldLabel: _('All categories available'),
                            allowBlank: false,
                            width: 200,
                            height: 100,
                            store: this.categories,
                            valueField: 'catid',
                            displayField: 'title',
                            msgTarget: 'side'
                        }, {
                            xtype: 'button',
                            text: _('Add'),
                            iconCls: 'icinga-icon-add',
                            fieldLabel: _('New category'),
                            handler: function(b, e) {
                                var c = this.categories;
                                
                                Ext.MessageBox.prompt(_('Add'), _('Add new category'), function(btn, text) {
                                    if (!Ext.isEmpty(text)) {
                                        var r = new c.recordType({
                                            title: text,
                                            catid: text,
                                            visible: true,
                                            position: 0,
                                            active: false
                                        });
                                        
                                        c.add(r);
                                    }
                                }, this);
                            },
                            scope: this
                            }]
                    }]
                }, {
                    region: 'east',
                    width: 420,
                    padding: '5px',
                    layout: 'form',
                    
                    items: [{
                        xtype: 'fieldset',
                        title: _('Image'),
                        defaults: { msgTarget: 'side' },
                        items: [this.iconCombo, {
                            xtype: 'panel',
                            border: false,
                            id: this.id + '-panel-icon-preview'
                        }]
                    }, {
                        xtype: 'fieldset',
                        title: _('Share your Cronk'),
                        labelWidth: 100,
                        height: 200,
                        items: this._roleCombo()
                    }]
                }, {
                    region: 'south',
                    id: this.id + '-expert-form',
                    
                    padding: '5px',
                    height: 350,
                    
                    layout: 'border',
                    
                    items: [{
                        region: 'center',
                        padding: '5px',
                        border: false,
                        
                        items: [{
                            xtype: 'fieldset',
                            title: _('Parameters'),
                            id: this.id + '-fieldset-parameters',
                            defaults: { 
                                border: false,
                                msgTarget: 'side'
                            },
                            items: [{
                                items: this.paramGrid
                            }]
                        }]
                    
                    }, {
                        region: 'east',
                        padding: '5px',
                        border: false,
                        width: 420,
                        
                        items: [{
                            xtype: 'fieldset',
                            title: _('Agavi setting'),
                            id: this.id + '-fieldset-agavi-settings',
                            defaults: {
                                width: 250,
                                msgTarget: 'side'
                            },
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: _('Module'),
                                name: 'module',
                                value: 'Cronks',
                                allowBlank: false
                            }, {
                                xtype: 'textfield',
                                fieldLabel: _('Action'),
                                name: 'action',
                                allowBlank: false
                            }, {
                                xtype: 'textarea',
                                name: 'state',
                                fieldLabel: _('State information'),
                                allowBlank: true,
                                height: 100
                            }]
                        }]
                    }]
                }]
            });
            
            formPanel.on('render', function(f) {
                this.on('resize', function(w, width, height) {
                    
                    var o = this.getSize();
                    this.formPanel.setSize(o.width-14, o.height);
                });
            }, this, { single: true });
            
            return formPanel;
            
        },
        
        showExpertMode : function(show) {
            
            var hdiff = 280;
            var hact = this.getHeight();
            
            show = (show===undefined) ? true : show;

            var frmExpert = Ext.getCmp(this.id + '-expert-form');       
            var fCronkId = this.formPanel.getForm().findField('cid');
            
            if (show === true) {
                frmExpert.show();
                fCronkId.show();
                
                if (hact < 680) {
                    this.setHeight(hdiff + hact);
                }
            }
            else {
                frmExpert.hide();
                fCronkId.hide();
                
                if (hact > 400) {
                    this.setHeight(hact - hdiff);
                }
            }
            
            this.center();
        },
        
        setCurrentCronkId : function(id) {
            
            this.resetForm();
            
            var cronk = Cronk.Registry.get(id);
            
            if (cronk) {
                this.cronkId = id;
                this.cronk = cronk;
                this.cronkCmp = Ext.getCmp(id);
                var params = Ext.apply({}, cronk.params);
                
                delete(params.action);
                delete(params.module);
                
                this.paramGrid.setSource(params || {});
                
                var form = this.formPanel.getForm();
                
                form.findField('name').setValue(this.cronkCmp.title);
                var cid = this.cronk.crname;
                if (!cid.match(/^CUSTOM-/)) {
                    cid = 'CUSTOM-' + this.cronk.crname;
                }
                // remove old id attachment
                if (cid.match(/\-?\d+$/)) {
                    cid = cid.replace(/\d+$/,'');
                }
                // generate random id and append it
                cid = cid + '-' + Math.floor((Math.random() * 100000000));
                form.findField('cid').setValue(cid);
                form.findField('module').setValue(this.cronk.params.module);
                form.findField('action').setValue(this.cronk.params.action);
                
                var cronkFrame = this.cronkCmp.get(0);
                
                this.updateState(cronk);
                
                if (cronkFrame && cronkFrame.stateful && cronkFrame.getState()) {
                    this.categories.on('load', function() {
                        form.findField('state').setValue(Ext.encode(cronkFrame.getState()));
                    }, this, { single: true});
                }
            }
        },
        
        updateState : function(cronk) {
            if (!Ext.isEmpty(cronk.statefulObjectId)) {
                var o = Ext.getCmp(cronk.statefulObjectId);
                if (o) {
                    this.categories.on('load', function() {
                        var f = this.formPanel.getForm();
                        f.findField('state').setValue(Ext.encode(o.getState()));
                    }, this, { single: true });
                }
            }
        },
        
        setCronkData : function(o) {
            
            this.resetForm();
            
            if (!Ext.isEmpty(o.image)) {
                o.image_id = o.image;
            }
            
            // The combo needs a image id to work properly
            if (o.image_id.match(/\/(\w+)\.\w{1,5}$/)) {
                o.image_id = RegExp.$1;
                o.image_id = 'cronks.' + o.image_id;
            }
            
            // Event driven because of hidden categories after edit
            this.categories.on('load', function() {
                var f = this.formPanel.getForm();
                
                f.findField('name').setValue(o.name);
                f.findField('description').setValue(o.description);
                f.findField('cid').setValue(o.cronkid);
                
                f.findField('module').setValue(o.module);
                f.findField('action').setValue(o.action);
                f.findField('state').setValue(o.state);
                
                f.findField('categories').setValue(o.categories);
                
                f.findField('image').setValue(o.image_id);
                this.refreshIconPreview();
                
                if (!Ext.isEmpty(o.groupsonly)) {
                    f.findField('share').setValue(true);
                    
                    // Overridden method @see js/widgets/Ext.ux.form.MultiSelect.Override.js
                    f.findField('roles').setValueByDisplayValues(o.groupsonly);
                }
                
                this.paramGrid.setSource(Ext.isObject(o['ae:parameter']) ? o['ae:parameter'] : {});
            }, this, { single: true });
            
        },
        
        resetForm : function() {
            var form = this.formPanel.getForm();
            form.items.each(function(item, index, a) {
                try {
                    item.setValue('');
                }
                catch (e) {}
            });
            
            this.paramGrid.setSource({});
        },
        
        refreshIconPreview : function() {
            
            var panel = Ext.getCmp(this.id + '-panel-icon-preview');
            
            if (panel.getEl().last()) {
                panel.getEl().last().remove();
            }
            
            var index = this.iconCombo.getStore().findExact('short', this.iconCombo.getValue());
            
            if (index>=0) {
                var record = this.iconCombo.getStore().getAt(index);
                
                panel.getEl().insertFirst({
                    tag: 'img',
                    src: record.data.web_path
                });
            }
        }
        
    });

    // For global singleton usage
    Cronk.util.CronkBuilder.getInstance = function() {
        if (!Ext.isDefined(Cronk.util.CronkBuilder.INSTANCE)) {
            Cronk.util.CronkBuilder.INSTANCE = new Cronk.util.CronkBuilder();
        }
        return Cronk.util.CronkBuilder.INSTANCE;
    };

    
})();
