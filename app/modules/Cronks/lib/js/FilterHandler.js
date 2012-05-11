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


/**
 * IcingaApiComboBox
 * Extended to let meta data from xml template
 * configure the store to fetch data from the IcingaAPI
 */
Cronk.IcingaApiComboBox = Ext.extend(Ext.form.ComboBox, {

    def_webpath : '/modules/web/api/json',
    def_sortorder : 'asc',

    constructor : function(cfg, meta) {

        var kf = meta.api_keyfield;     // ValueField
        var vf = meta.api_valuefield;   // KeyField

        var fields = [];
        var cols = [];

        var cfields = {};
        cfields[kf] = true;
        cfields[vf] = true;

        if (meta.api_id) {
            cfields[meta.api_id] = true;
        }

        // If we need more fields to work with
        if (meta.api_additional) {
            var i = meta.api_additional.split(',');
            for (var k in i) {
                if (Ext.isString(i[k])) {
                    cfields[i[k]] = true;
                }
            }
        }

        for (var f in cfields) {
            cols.push(f);
            fields.push({
                name: f
            });
        }

        var apiStore = new Ext.data.JsonStore({
            autoDestroy : true,
            url : AppKit.c.path + this.def_webpath,

            root : 'result',

            baseParams : {
                target : meta.api_target,
                order_col: (meta.api_order_col || meta.api_keyfield),
                order_dir: (meta.api_order_dir || this.def_sortorder),
                columns: cols
            },

            idProperty : (meta.api_id || meta.api_keyfield),

            fields : fields,
            
            listeners : {
                beforeload : function(store, options) {
                    if (!Ext.isEmpty(store.baseParams.query)) {
                        store.baseParams.filters_json = Ext.util.JSON.encode({
                            type : 'AND',
                            field : [{
                                type : 'atom',
                                field : [vf],
                                method : ['like'],
                                value : [String.format('*{0}*', store.baseParams.query)]
                            }]
                        });
                    }
                }
            }
        });
        
        apiStore.load();
        
        

        cfg = Ext.apply(cfg || {}, {
            store : apiStore,
            displayField: vf,
            valueField : vf,
            keyField : kf
        });

        // To display complex multi column layouts
        if (meta.api_exttpl) {
            cfg.tpl = '<tpl for="."><div class="x-combo-list-item">' + meta.api_exttpl + '</div></tpl>';
        }

        // Notify the parent class
        Cronk.IcingaApiComboBox.superclass.constructor.call(this, cfg);
        
        
    }
});

// Our class
Cronk.FilterHandler = function() {
    Cronk.FilterHandler.superclass.constructor.call(this);
};

// Extending
Cronk.FilterHandler = Ext.extend(Ext.util.Observable, {
    
    oFilterOp : {
        'appkit.ext.filter.text': 'text',
        'appkit.ext.filter.number': 'number',
        'appkit.ext.filter.servicestatus': 'number',
        'appkit.ext.filter.hoststatus': 'number',
        'appkit.ext.filter.bool': 'bool'
    },
    
    oOpList : {
        text: [
        [60, _('contain')],
        [61, _('does not contain')],
        [50, _('is')],
        [51, _('is not')]
        ]   ,
        
        number: [
        [50, _('is')],
        [51, _('is not')],
        [70, _('less than')],
        [71, _('greater than')]
        ],
        
        bool: [
        [50, _('is')]
        ]
    },
    
    oOpDefault : {
        number: 50,
        text: 60,
        bool: 50
    },
    
    meta : {},
    config : {},
    
    cList : {},
    
    constructor : function(config) {
        
        Ext.apply(this.config, config);
        
        if (this.config.meta) {
            this.setMeta(this.config.meta);
        }
        
        this.listener = {};
        
        this.addEvents({
            'aftercompremove' : true,
            'compremove' : true,
            'aftercompadd' : true,
            'compcreate' : true,
            'aftercompcreate' : true,
            'metaload' : true
        });
        
        Cronk.FilterHandler.superclass.constructor.call();
    },
    
    setMeta : function (meta) {
        if (tis.fireEvent('metaload', this, meta) !== false) {
            this.meta = meta;
        }
        
        return true;
    },
    
    getRemoveComponent : function(meta) {
        var button = new Ext.Button({
            xtype: 'button',
            iconCls: 'icinga-icon-cross',
            handler: function(b, e) {
                this.removeComponent(meta);
            },
            anchor: '-5',
            scope: this,
            width:25,
            columnWidth: '25px'
        });
        
        return button;
    },
    
    removeAllComponents : function() {
        Ext.iterate(this.cList, function(k, v) {
            this.removeComponent(v);
        }, this);
    },
    
    removeComponent : function(meta) {
        
        var cid = 'fco' + meta.id;
        
        // Retrieve the comp_id
        var p = Ext.getCmp('fco' + meta.id);
            
        // Removing the panel construct
        if (this.fireEvent('compremove', this, p, meta) !== false) {
                
            var form = p.findParentByType('form');
                
            if (form) {
                form.remove(p, true).destroy();
                delete this.cList[cid];
            }
        }
            
        this.fireEvent('aftercompremove', this, p, meta);

        return true;
    },
    
    getLabelComponent : function(meta) {
        return {
            xtype: 'label',
            html: meta['label'],
            border: false,
            width: "100px",
            columWidth: "100px"
        };
    },
    
    getOperatorComponent : function(meta) {
        var  type = null;
        
        // Disable the operator
        if (meta.no_operator && meta.no_operator == true) {
            return new Ext.Panel({
                border: false
            });
        }
        
        if (meta.operator_type) {
            type = meta.operator_type;
        }
        
        if (!type) {
            type = this.oFilterOp[meta.subtype];
        }
        
        // this is our combo field
        var oCombo = new Ext.form.ComboBox({
            
            store : new Ext.data.ArrayStore({
                idIndex : 0,
                fields : ['id', 'label'],
                data : this.oOpList[type] || []
            }),
            
            mode : 'local',
            
            typeAhead : true,
            triggerAction : 'all',
            forceSelection : true,
                    
            fieldLabel : "Operator",
            
            valueField : 'id',
            displayField : 'label',
            
            hiddenName : meta.id + '-operator',
            hiddenId : meta.id + '-operator',
            
            'name' : '___LABEL' + meta.id + '-operator',
            id : '___LABEL' + meta.id + '-operator',
            columnWidth: .3

            
        });
        
        // Set the default value after rendering
        oCombo.on('render', function(c) {
            c.setValue(this.oOpDefault[type]);
        }, this);
        
        // Pack all together in a container
        // var p = new Ext.Panel({border: false});
        // p.add(oCombo);
        // return p;
        
        return oCombo;
    },
    
    getComboComponent : function(data, meta) {
        var def = {
            store: new Ext.data.ArrayStore({
                idIndex: 0,
                fields: ['fId', 'fStatus', 'fLabel'],
                data: data
            }),
            
            'name': '__status_name_' + meta.name,
            'id': '__status_name_' + meta.name,
            // 'name': meta.name + '-value',
            
            mode: 'local',
            typeAhead: true,
            triggerAction: 'all',
            forceSelection: true,
            
            
            fieldLabel: 'Status',
            
            valueField: 'fStatus',
            displayField: 'fLabel',
            
            hiddenName: meta.name + '-value',
            hiddenId: meta.name + '-value',
            columnWidth: .6
        };
        
        return new Ext.form.ComboBox(def);
        
    },

    getApiCombo : function(meta) {
        return new Cronk.IcingaApiComboBox({
            typeAhead: false,
            triggerAction: 'all',
            forceSelection: false,
            'name': meta.name + '-field',
            'id': meta.name + '-field',
            hiddenName: meta.name + '-value',
            hiddenId: meta.name + '-value',
            columnWidth: .6
        }, meta);
    },
    
    getFilterComponent : function(meta) {
        var oDef = {
            'name' : meta.name + '-value',
            id : meta.name + '-value',
            columnWidth: .6,
            bodyStyle:'padding:0 18px 0 0'
        };
        
        switch (meta.subtype) {
            
            case 'appkit.ext.filter.servicestatus':
                return this.getComboComponent([
                    ['1', '0', 'OK'],
                    ['2', '1', 'Warning'],
                    ['3', '2', 'Critical'],
                    ['4', '3', 'Unknown']
                    ], meta);
                break;
            
            case 'appkit.ext.filter.bool':
                return this.getComboComponent([
                    ['1', '1', _('Yes')],
                    ['2', '0', _('No')]
                    ], meta);
                break;
            
            case 'appkit.ext.filter.hoststatus':
                return this.getComboComponent([
                    ['1', '0', 'UP'],
                    ['2', '1', 'Down'],
                    ['3', '2', 'Unreachable']
                    ], meta);
                break;

            case 'appkit.ext.filter.api':
                return this.getApiCombo(meta);
                break;

            default:
                return new Ext.form.TextField(oDef);
                break;
            
        }
        
        
    },
    
    createComponent : function(meta) {
        return this.componentDispatch(meta);
    },
    
    componentDispatch : function(meta) {
        
        var cid = 'fco' + meta.id;
        
        var panel = new Ext.Panel({
            id: cid,
            border: false,
            layout: 'column',
            padding:4,
            defaults: {
                border: false,
                anchor: '100%'
            }
        });
        
        // Before adding stage
        if (this.fireEvent('compcreate', this, panel, meta) !== false) {
            
            this.cList[cid] = meta;
            
            // Adding the label
            panel.add([
                this.getLabelComponent(meta),
                this.getOperatorComponent(meta),
                this.getFilterComponent(meta),
                this.getRemoveComponent(meta)
                ]);
            
        }
        
        // All panels there
        this.fireEvent('aftercompcreate', this, panel, meta);
        
        return panel;
        
    }
    
});

// Adding the blank events
Ext.apply(Cronk.FilterHandler, {
    afterCompRemove : function(fh, p, meta) {
        return true;
    },
    
    compRemove : function(fh, p, meta) {
        return true;
    },
    
    afterCompCreate : function(fh, panel, meta) {
        return true;
    },
    
    compCreate : function(fh, panel, meta) {
        return true;
    },
    
    metaLoad : function(fh, meta) {
        return true;
    }
});
