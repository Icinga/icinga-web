Ext.ns('Cronk.grid');


Cronk.grid.AdvancedHostStatusColumnRenderer = new (function () { 

    /**
    * Iterates through the displayed lines and updates unhandled problems counter in view
    * 
    * @param array  The result returned from the REST-Api call
    * @param Ext.grid.GridPanel The grid to update
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/ 
    var updateHostServiceData = function (result,grid) {
        var hostcols = grid.getEl().select("div[host_object_id]"); 
        
        for(var i=0;i<result.length;i++) {
            var current = result[i];
             
            hostcols.each(function(elem) {
                 
                if(current.HOST_OBJECT_ID != elem.getAttribute("host_object_id")) 
                    return true;
            
                if(current.SERVICE_STATE == 1 && elem.hasClass('icinga-status-warning-disabled')) {
                    elem.replaceClass('icinga-status-warning-disabled', 'icinga-status-warning');
                    elem.dom.innerHTML = current.COUNT;
                    return false;
                } 
                if(current.SERVICE_STATE == 2 && elem.hasClass('icinga-status-critical-disabled')) {
                    elem.replaceClass('icinga-status-critical-disabled', 'icinga-status-critical');
                    elem.dom.innerHTML = current.COUNT;
                    return false;
                }
            },this);
        } 
    };
    
    /**
    * Lazy loads open problems which will be injected into the status view after success
    *
    * @param Ext.grid.GridPanel The gridpanel to select the statusfields from
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    var loadServiceStateTask = function(grid) { //new Ext.util.DelayedTask(function(grid) {
        
        // REST Call
        Ext.Ajax.request({
            url: AppKit.c.path+"/web/api/json",
            params: {
                target: 'service_status_summary', 
                "columns[0]": "host_object_id",
                "columns[1]": "current_state",
                filters_json: Ext.encode({
                    type: 'AND',
                    field: [{
                        type: 'atom',
                        field: ['SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED'],
                        method: ['='],
                        value: ['0']
                    },{
                        type: 'atom',
                        field: ['SERVICE_SCHEDULED_DOWNTIME_DEPTH'],
                        method: ['='],
                        value: ['0'],
                        type:'atom'
                    },{
                        type:'atom',
                        field: ['current_state'],
                        method: ['>'],
                        value: [0]
                    }]
                }),
                group: ['host_object_id']
            }, 
            success: function(response) {
                try {
                    var json = Ext.decode(response.responseText);
                    updateHostServiceData(json.result,grid);             
                                      
                } catch(e) {
                    AppKit.log("Loading servicestate failed: "+e); 
                  
                }
             
            },
           
            scope: this
        });            
    };
    /**
    *   Start point for the grid evenlistener
    *   @param Ext.grid.GridPanel   The gridpanel that should be extended by this columnRenderer
    *
    *   @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    this.init = function(grid) {
        var store = grid.getStore(); 
        store.on("load", function() {
            loadServiceStateTask(grid);
        },this);
    };
    
    /**
    * The columnrenderer definition, should be in the renderer column of the cronk tempalte
    * 
    * @return A columnRenderer instance
    **/
    this.hostStatus = function() {
    	return function(value, metaData, record, rowIndex, colIndex, store) {
			if(Ext.isDefined(record.json.host_is_pending)) {
				if(record.json.host_is_pending > 0)
					value=99;
			}
			if(!Ext.isDefined(value))
				return "";
            
			return Icinga.StatusData.wrapExtendedElement('host', value,null,null,{criticals: '-', warnings: '-',object_id: record.get('host_object_id')});
		}


    };
})();

