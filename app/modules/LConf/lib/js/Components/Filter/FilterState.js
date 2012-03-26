Ext.ns("LConf.Filter").FilterState = function() {
    this.activeFilters = [];
    this.eventDispatcher = null;
    this.bypassed = false;
    this.dStore = null;
    this.urls = null;
    this.active = true;

    this.constructor = function(cfg) {
        this.urls = cfg.urls;
        this.eventDispatcher = cfg.eventDispatcher;
    }

    this.bypassAll = function() {
        this.bypassed = this.getActiveFilters();
        this.active = false;
        this.deactivateAll();
    }

    this.removeBypass = function() {
        this.activateFilter(this.bypassed);
        this.bypassed = false;
        this.active = true;
    }

    this.getActiveFilters = function() {
		if(this.activeFilters)
			return this.activeFilters;
		return [];
	},
    
    this.activateFilter = function(filter) {
        if(!this.activeFilters)
            this.activeFilters = [];

        if(Ext.isArray(filter))
            this.activeFilters = this.activeFilters.concat(filter)
        else            
            this.activeFilters.push(filter);

        this.eventDispatcher.fireCustomEvent("filterChanged",this.activeFilters,this);
    },

    this.deactivateFilter = function(filter) {
		var removed = false;
		do {
			removed = false;
			Ext.each(this.activeFilters,function(curfilter,idx,all) {
				if(curfilter == filter) {
					this.activeFilters.splice(idx,1);
					removed = true;
				}
			},this);
		} while(removed);
		this.eventDispatcher.fireCustomEvent("filterChanged",this.activeFilters,this);
	},

    this.deactivateAll = function() {
        this.activeFilters = [];
		this.eventDispatcher.fireCustomEvent("filterChanged",this.activeFilters,this);
	},

    this.getStore = function() {
        if(this.dStore === null) {
            this.dStore = new Ext.data.JsonStore({
                autoLoad:true,
                root: 'result',
                autoSave:false,
                proxy: new Ext.data.HttpProxy({
                    url: this.urls.modifyfilter,
                    api: {
                        'read': this.urls.filterlisting
                    }
                }),
                listeners: {
                    // Check for errors
                    exception : function(prox,type,action,options,response,arg) {
                        if(response.status == 200)
                            return true;
                        response = Ext.decode(response.responseText);
                        if(response.error.length > 100)
                            response.error = _("A critical error occured, please check your logs");
                        Ext.Msg.alert(_("Error"), response.error);
                        return true;
                    },
                    save: function(store) {
                        store.load();
                    }
                },
                writer: new Ext.data.JsonWriter(),
                autoDestroy:true,
                fields: [
                    'filter_id','filter_name','filter_json','filter_isglobal'
                ],
                idProperty:'filter_id',
                root: 'filters'
            })
        }
        return this.dStore;
    }

    this.saveFilter = function(obj,text,record) {
        var json = Ext.encode(obj);
        var store = this.getStore();
        var add = false;
        if(!record) {
            add = true;
            record = new store.recordType();
        }
        record.set('filter_json',json);
        record.set('filter_name',text);
        record.set('filter_isglobal',false);
        if(add)
            store.add(record);
        this.deactivateAll();
        store.save();
    }

    this.constructor.apply(this,arguments);
}