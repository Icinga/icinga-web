Ext.onReady(function() {
    Ext.ns('Cronk.bp');
    /**
     * Class that manages filters for the business process adapter cronk
     * The actual filtering is performed in the treegrid, so this only stores the filter types
     * which should be applied
     */
        AppKit.log("Filtermanager defined ");
    Cronk.bp.filterManager = Ext.extend(Ext.util.Observable,{
        constructor: function(cfg) {
            cfg = cfg || {};
            Ext.apply(this,cfg);
            Ext.util.Observable.prototype.constructor.call(this,cfg);
            this.addEvents({
                "filterChanged" : true
            });

            if(cfg.filterString) {
                this.buildFiltersFromString(cfg.filterString);
            }
        },

        oOpList :  {
            text: [
                [60, 'contain'],
                [61, 'does not contain'],
                [50, 'is'],
                [51, 'is not']
            ],

            number: [
                [50, 'is'],
                [51, 'is not'],
                [70, 'less than'],
                [71, 'greater than']
            ],

            bp: [
                ['belongs', 'to'],
                ['belongs_not', 'not to']
            ]
        },

        filterMap: {
            'Belongs' : 'display_name',
            'Name' : 'display_name',
            'Hardstate' : 'origState',
            'Host' : 'Host',
            'Service' : 'Service',
            'Priority' : 'display_prio',
            'CustomVariable' : {target:'customvariable',field:'CUSTOMVARIABLE_NAME',field2:'CUSTOMVARIABLE_VALUE'}
        },

        availableProperties : {
            'Belongs' : [],
            'Name' : [],
            'Hardstate': [],
            'Host' :[],
            'Service': [],
            'Priority' : []
        },

        filterProperties : new Ext.data.ArrayStore({
            autoDestroy:false,
            idIndex: 0,
            fields:['Name','Type','api_target','api_field','api_secondary_field'],
            data: [
                ['Belongs','bp'],
                ['Name','text'],
                ['Hardstate','text'],
                ['Priority','number'],
                ['CustomVariable','API','customvariable','CUSTOMVARIABLE_NAME','CUSTOMVARIABLE_VALUE']
            ]
        }),

        activeFilters : {},
        /**
         * Registers a new filter
         * if noEvent is true, no filterChanged Event will be fired
         */
        addFilter : function(filter,noEvent) {
            var filterId = Ext.id(null,"bp_filter");
            this.activeFilters[filterId] = Ext.apply(filter,{
                field: this.filterMap[filter["name"]]
            });

            if(!noEvent) {
                this.fireEvent("filterChanged",this.getFilters());
            }

            return filterId;
        },

        hasAPIFilter : function() {
            for(item in this.activeFilters) {
                if(Ext.isObject(this.activeFilters[item].field))	{
                    return true;
                }
            }
            return false;
        },

        getAPIFilters: function() {
            var APIFilters = [];
            for(item in this.activeFilters) {
                if(Ext.isObject(this.activeFilters[item].field))	{
                    APIFilters.push(this.activeFilters[item]);
                }
            }
            return APIFilters;
        },

        buildFiltersFromString: function(str) {
            var filterSet = str.split(";");
            Ext.each(filterSet,function(filter) {
                var filterParts = filter.split("|");
                if(filterParts.length != 3) {// skip if it doesn't fit
                    AppKit.notifyMessage(_('Filter error'),('Filter ')+filter+_(' is invalid'));
                    return true;
                }
                if(!this.availableProperties[filterParts[0]]) {
                    AppKit.notifyMessage(_('Filter error'),('Filter ')+filter+_(' is invalid'));
                    return true;
                }

                // resolve operator
                var ops = this.filterProperties.getById(filterParts[0]).get('Type');
                var opToUse = null;
                Ext.each(this.oOpList[ops],function(operator) {

                    if(operator[1] == filterParts[1]) {
                        opToUse = operator[0];
                        return false;
                    }
                },this);
                if(!opToUse) {
                    AppKit.notifyMessage(_('Filter error'),('Filter ')+filter+_(' is invalid'));
                    return true;
                }
                // Add filter
                this.addFilter(filterParts[0],opToUse,filterParts[2]);
            },this);
        },

        removeFilter : function(id,noEvent) {
            this.activeFilters[id] = null;
            if(!noEvent) {
                this.fireEvent("filterChanged",this.getFilters());
            }
        },
        removeAll : function(noEvent) {
            this.activeFilters = {};
            if(!noEvent) {
                this.fireEvent("filterChanged",this.getFilters());
            }
        },

        getFilters : function() {
            return this.activeFilters;
        },
        /**
         * Add filters from values returned by a Ext.form.BasicForm's getFieldValue method
         * fires filterChanged
         */
        applyFilters : function(values) {
            this.removeAll(true);

            if(!Ext.isArray(values.field)) {
                values.field = [values.field];
                values.operator = [values.operator];
                values.value = [values.value];
                values.value2 = values.value2 ? [values.value2] : '';
            }

            for(var i=0;i<values.field.length;i++) {
                this.addFilter({
                    name: values["field"][i],
                    operator: values["operator"][i],
                    value: values["value"][i],
                    value2: values["value2"] ? values["value2"][i]: null
                },true);
            }
            this.fireEvent("filterChanged",this.getFilters());
        },

        getAddRestrictionField : function(id) {
            return {
                xtype:'combo',
                store: this.filterProperties,
                mode:'local',
                displayField: 'Name',
                valueField:'Name',
                fieldLabel: _('Add restriction'),
                listeners: {
                    'select' : function(cmb,val) {
                        this.addFilterfield(val,Ext.getCmp('fpanel_filter_'+id));
                        cmb.clearValue();
                    },
                    scope: this
                }
            };
        },

        filterWindow : function() {
            var id = Ext.id();
            new Ext.Window({
                layout:'fit',
                width:700,
                height:200,
                title: _('Add filters'),
                id: 'wnd_filter_'+id,
                items:new Ext.form.FormPanel({
                    autoScroll:true,
                    width:600,
                    id: 'fpanel_filter_'+id,
                    padding:5,
                    items: [this.getAddRestrictionField(id)]
                }),
                buttons: [{
                    // Save button
                    iconCls: 'icinga-icon-accept',
                    text: _('Apply'),
                    handler: function() {
                        // Save and close
                        var fpanel = Ext.getCmp('fpanel_filter_'+id);
                        if(fpanel.getForm().isValid()) {
                            var values = fpanel.getForm().getFieldValues();
                            this.applyFilters(values);

                            Ext.getCmp('wnd_filter_'+id).close();
                        }
                    },
                    scope: this
                },{
                    // Discard button
                    iconCls: 'icinga-icon-cross',
                    text: _('Discard'),
                    handler: function(btn) {
                        Ext.getCmp('wnd_filter_'+id).close();
                    },
                    scope: this
                },{xtype:'tbseparator'}, {
                    // Delete button
                    iconCls: 'icinga-icon-delete',
                    text: _('Reset'),
                    handler: function() {
                        // Removes all filters from the panel
                        var fpanel = Ext.getCmp('fpanel_filter_'+id);
                        fpanel.removeAll();
                        fpanel.add(this.getAddRestrictionField(id));
                        fpanel.doLayout();
                    },
                    scope:this
                }],
                renderTo:Ext.getBody(),
                modal:true
            }).show();
            var cmp = Ext.getCmp('fpanel_filter_'+id);
            var filters = this.getFilters();
            for(var filterId in filters) {
                var filter = filters[filterId];

                this.addFilterfield(this.filterProperties.getById(filter.name),cmp,filter);
            }
        },

        /**
         * Adds a new line for adding filters
         * @param Ext.data.Record value The filter descriptor
         *		Contains:
         *			- Id   : The Id, generally the filter name
         *			- Type : The filter type (text, numeric, etc)
         *			- Name : The Name of the list to filter to (for loading preset values)
         *
         * @param Object container The container to add the filter field to
         * @param Object preset Preset values to insert
         *
         * @return Ext.form.ComboBox
         */
        addFilterfield : function(value,container,preset) {
            preset = preset || {};
            if(!value) {
                return false;
            }
            value = value || preset.field;

            var filterId = Ext.id('filterField');

            var opField = this.getOperatorField(value.get('Type'),(preset["value2"] ? preset["value2"] : preset["operator"]),value);
            var valField = this.getValueField(value.get('Name'),preset["value"],value.get('Type') == 'API' ? opField : null,value);
            var items = [{
                xtype:'textfield',
                readOnly:true,
                name: 'field',
                width:100,
                allowBlank:false,
                value: value.id
            }, {
                items:opField
            },{
                items:valField
            }, {
                items: this.getRemoveComponent(filterId,container)
            },{
                items:{xtype:'checkbox',name:'isAPI', checked : value.get('Type') == 'API',hidden:true}
            }];
            // Add relation operator if it's a custom var

            /**
             * @ToDo: That's way too specific, when there are new fields that also require special handling,
             * then refactor
             */
            var additionalOperator = 
                (value.get('Type') == 'API' && value.get('api_secondary_field')) ?
                    this.getOperatorField('text',preset["operator"]) :
                    null;

            if(additionalOperator) {
                items.splice(2,0,additionalOperator);
            } else {
                items.splice(2,0,{xtype:'hidden',width:125,name:'value2',value: ''});
            }
            var filter = new Ext.Panel({
                layout:'column',
                id: filterId,
                border: false,
                style: 'padding: 2px;',
                defaults: {
                    border: false,
                    style: 'padding: 2px;'
                },
                items: items
            });
            container.add(Ext.clean(filter));
            container.doLayout();
        },


        /**
         * Returns a Ext.form.ComboBox with the appropriate operators (or a CV selection)
         * @param string type The field type (for example, text)
         * @param string pre  The preset written in the field
         *
         * @return Ext.form.ComboBox
         */
        getOperatorField : function(type,pre,value) {
            // @ToDo: Not nice to add a special field here for one use case, refactor if there is further adjustment needed
            var oCombo;
            if(type == 'API') {
                oCombo  = this.getAPIDrivenComboBox({
                    target: value.get('api_target'),
                    field: value.get('api_field'),
                    display: value.get('api_field'),
                    name: 'value2'
                },pre);
                return oCombo;
            }
            oCombo = new Ext.form.ComboBox({
                store : new Ext.data.ArrayStore({
                    idIndex : 0,
                    fields : ['id', 'label'],
                    data : this.oOpList[type] || [],
                    triggerAction : 'all'
                }),

                mode : 'local',
                name:'operator',
                typeAhead : true,
                triggerAction : 'all',
                forceSelection : true,
                allowBlank:false,
                fieldLabel : "Operator",
                value: pre,
                valueField : 'id',
                displayField : 'label',
                width:125
            });

            return oCombo;
        },

        /**
         * Creates a combobox that automatically requests it's presets from the Icinga API
         * @param Object cfg An object describing how and what to request
         *		contains:
         *			filters: Either an object with {column : '' , relation: '', value : ''} or an
         *					 Ext.data.ComboBox object
         *			target : The API target field (host,service,etc.)
         *			field  : The column to request
         *
         * @returns Ext.data:ComboBox
         */
        getAPIDrivenComboBox: function(cfg,pre) {
            var filters = {};
            if(Ext.isArray(cfg.filters)) {
                var i =0 ;
                Ext.each(cfg.filters,function(filter){
                    filters["filters["+i+"][column]"] = filter.column;
                    filters["filters["+i+"][relation]"] = filter.relation;
                    filters["filters["+i+"][value]"] = filter.value;
                });
            }

            var cmb =  new Ext.form.ComboBox({
                store: new Ext.data.JsonStore({
                    url: this.icingaApiURL,
                    baseParams: Ext.apply(filters,{
                        "columns[]": cfg.field,
                        "target": cfg.target,
                        "output" : 'json',
                        'withMeta' : true, // metaData is provided by the server
                        'successProperty' : 'success'
                    }),

                    listeners: {
                        // Update to store values before loading
                        beforeload: function(store,options) {
                            if(!cfg.filters) {
                                return true;
                            }
                            if(cfg.filters.ctype) {
                                if(!cfg.filters.getValue()) {
                                    return false;
                                }
                                options.params["filters["+i+"][column]"] = cfg.filters.valueField;
                                options.params["filters["+i+"][relation]"] = "=";
                                options.params["filters["+i+"][value]"] = cfg.filters.getValue();
                            }
                            return true;
                        }
                    },
                    autoLoad:true
                }),
                width:125,
                allowBlank:false,
                displayField: cfg.display,
                valueField: cfg.value || cfg.display,
                value: pre,
                name: cfg.name,
                triggerAction: 'all',
                mode: 'remote'

            });
            // clear this field on reset
            if(cfg.filters) {
                if(cfg.filters.ctype) {
                    cfg.filters.on("change",function() {
                        cmb.clearValue();
                        cmb.getStore().load();
                    });
                }
            }
            return cmb;
        },

        getValueField : function(id,pre,rel,value) {
            var oCombo;
            if(rel) {
                oCombo  = this.getAPIDrivenComboBox({
                    target: value.get('api_target'),
                    field: value.get('api_secondary_field'),
                    display: value.get('api_secondary_field'),
                    name: 'value',
                    filters: rel
                },pre);
                return oCombo;
            }
            oCombo = new Ext.form.ComboBox({
                store : new Ext.data.ArrayStore({
                    idIndex : 0,
                    fields : ['name'],
                    data : this.availableProperties[id]
                }),

                name:'value',
                mode : 'local',
                typeAhead : true,
                triggerAction : 'all',
                forceSelection : false,
                allowBlank:false,
                fieldLabel : "Value",
                displayField: 'name',
                valueField : 'name',
                columnWidth: .3,
                width:120,
                value: pre
            });

            return oCombo;
        },

        getRemoveComponent : function(id,container) {
            var button = new Ext.Button({
                xtype: 'button',
                iconCls: 'icinga-icon-cross',
                handler: function(b, e) {
                    container.remove(id);
                    container.doLayout();
                },
                columnWidth: .1,
                scope: this
            });

            return button;
        }
    });
});