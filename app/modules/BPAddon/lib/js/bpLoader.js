Ext.onReady(function() {
    Ext.ns("Cronk.bp")

    /**
     * Extended TreeLoader that handles the JSON format returned by the nagiosBP
     * Addon. Does a lot of rewriting in order to get the TreeGrid working
     */
    Cronk.bp.bpLoader = Ext.extend(Ext.ux.tree.TreeGridLoader,{
         sortDir : 'ASC',
         /**
          * Show AND/OR/MIN groups in tree
          */
         withGroups: false,
         hostAddress: window.location.protocol+"//"+window.location.host+"/",
         /**
          * Handles the response, reformats the json and creates nodes
          */
         processResponse : function(response, node, callback, scope){
            var json = response.responseText;
            try {
                var o = response.responseData || Ext.decode(json);
                if(o["business_processes"])
                    o = o["business_processes"];
                // Reformat data
               }catch(e){
                this.handleFailure(response);
            }
            o = this.formatData(o);
            try {
                node.beginUpdate();
                for(var i = 0, len = o.length; i < len; i++) {
                    // Always change the id, a* If checkchild is true s doubled id's cause confusion for the treegrid
                    o[i].id = Ext.id('xnode');

                    var n = this.createNode(o[i]);					
                    if(n){
                        node.appendChild(n);
                    }
                }
                node.endUpdate();
                this.runCallback(callback, scope || node, [node]);
             }catch(e){
                this.handleFailure(response);
            }
        },


        /**
         * The requestData function must be extended with the authentification header,
         * that's why it's implented here again 
         */
        requestData : function(node, callback, scope){

            if(this.fireEvent("beforeload", this, node, callback) !== false){false
                if(this.directFn) {
                    var args = this.getParams(node);
                    args.push(this.processDirectResponse.createDelegate(this, [{callback: callback, node: node, scope: scope}], true));
                    this.directFn.apply(window, args);
                } else{
                    this.transId = Ext.Ajax.request({
                        method:this.requestMethod,
                        // Is there a custom config? Then use it, otherwise don't use the conf param
                        url: this.hostAddress+this.dataUrl+(this.bp_config ? "&conf="+this.bp_config : ''),
                        success: this.handleResponse,
                        failure: this.handleFailure,
                        defaultHeaders: { // add auth header
                            'Authorization': this.authKey
                        },
                        scope: this,
                        argument: {callback: callback, node: node, scope: scope},
                        params: this.getParams(node)
                    });
                }
            }else{
                // if the load is cancelled, make sure we notify
                // the node that we are done
                this.runCallback(callback, scope || node, []);
            }
        },

        /**
         * Formats the json delivered by the nagiosBP Addon for use with the
         * Ext.ux.tree.TreeGrid Panel
         */
        formatData: function(obj) {	
            //try {
                var data = [];
                // Add/Rename needed fields
                for(var _name in obj) {
                    var objToAdd = obj[_name];
                    this.formatEntry(objToAdd,_name,obj);

                    data.push(objToAdd);
                }
                // Traverse through tree and build child node
                for(var _name in obj) {
                    var objToAdd = obj[_name];
                    this.buildChildren(objToAdd,_name,obj);
                }
                // If groups should be shown, add them to the tree
                if(this.withGroups)
                    for(var _name in obj) {
                        var objToAdd = obj[_name];
                        this.buildRelationGroups(objToAdd,_name,obj);
                    }

                return data;
            /*} catch(e) {
                Ext.MessageBox.alert(_("An error occured"),_("An error occured while parsing: "+e));			
                return [];
            }*/

        },
        /**
         * Creates a copy of an object, in order to create aliases
         */
        copyOf: function(obj,_name,all) {

            if(Ext.isObject(obj)) {
                var copy = {};
                for(var i in obj) {
                    copy[i] = this.copyOf(obj[i]);
                }
                if(copy["children"])
                    this.buildChildren(copy,_name,all);
                return copy;
            } else if(Ext.isArray(obj)) {
                var copy = [];
                for(var i=0;i<obj.length;i++) {
                    copy[i] = this.copyOf(obj[i]);
                }

                return copy;
            } else 
                return obj;
        },

        /**
         * Formats the raw json data returned by the nagiosBp Plugin and 
         */
        formatEntry: function(objToAdd,_name,all) {
       //     try {
                if(objToAdd["isProcessed"]) {
                    return this.copyOf(objToAdd,_name,all);
                }
                if(objToAdd["hardstate"] == null)
                    objToAdd["hardstate"] = "UNKNOWN";
                var state = objToAdd["hardstate"];
                objToAdd["origState"] = state;
                objToAdd["hardstate"] = '<div class="icinga-status icinga-status-'+state.toLowerCase()+'" style="height:12px;text-align:center">'+state+'</div>';
                objToAdd["business_process"] = _name;
                objToAdd["uiProvider"] = 'col';
                objToAdd["iconCls"] = 'icinga-icon-chart-organisation';
                objToAdd["children"] = objToAdd["components"];
                if(objToAdd["info_url"])
                    objToAdd["info_url"] = '<a href="'+objToAdd['info_url']+'" '+
                        'qtip="Show info_url" class="icinga-icon-information icinga-icon-24"></a>';

                objToAdd["isProcessed"] = true;
                if(this.withGroups)

                // Add possible values to filter manager, so it's available in the combo boxes
                this.filterManager.availableProperties['Belongs'].push([objToAdd["display_name"]]);
                this.filterManager.availableProperties['Hardstate'].push([state]);
                this.filterManager.availableProperties['Name'].push([objToAdd["display_name"]]);
                this.filterManager.availableProperties['Priority'].push([objToAdd["display_prio"]]);

                return objToAdd;
            /*} catch(e) {
                Ext.MessageBox.alert(_("An error occured"),_("An error occured while parsing: "+e));

            }*/
        },

        /**
         * Formats the subprocesses of a business process (i.e. services or other
         * business processes)
         */
        buildChildren : function(objToAdd,_name,all) {
            // Shouldn't happen, but let's be sure
            if(!objToAdd["children"])
                return true;

            for(var i=0;i<objToAdd["children"].length;i++) {
                var child = objToAdd["children"][i]; 
                /**
                 * Distinguish between subprocess or service
                 */
                if(child["subprocess"]) {
                    // If it's a subprocess, handle it like ever other subprocess
                    child = this.formatEntry(all[child["subprocess"]],child["subprocess"],all);
                    child["iconCls"] = 'icinga-icon-chart-organisation';
                    if(child["info_url"])
                        child["info_url"] = 
                            '<a href="'+child['info_url']+'" '+
                                ' qtip="Show info_url" class="icinga-icon-information icinga-icon-24">link</a>';

                    objToAdd["children"][i] = child;
                } else  {
                    try {
                        child["uiProvider"] = 'col';
                        if(!child["display_name"]) {

                            if(child["service"] && child["host"])
                                child["display_name"] = child["host"]+" : "+child["service"];

                            // Add possible values to filter manager
                            this.filterManager.availableProperties['Host'].push([child['host']]);
                            this.filterManager.availableProperties['Service'].push([child['service']]);
                            this.filterManager.availableProperties['Name'].push([child['display_name']]);
                            child["host_name"] = child["host"] || null;
                            child["service_name"] = child["service"] || null;
                            child["iconCls"] = 'icinga-icon-cog';

                            if(!child["hardstate"])
                                child["hardstate"] = 'UNKNOWN';
                            // Create selectors for links
                            child["origState"] = child["hardstate"];
                            child["service"] =
                                '<span qtip="Show services for this host" class="bp_service_selector x-icinga-grid-link" '+
                                    'host="'+child["host"]+'" '+
                                    'service="'+child["service"]+'">'+
                                    child["service"]+
                                    '</span>';
                            child["host"] =
                                '<span qtip="Show host" class="bp_host_selector x-icinga-grid-link" '+
                                    'host="'+child["host"]+'">'+
                                    child["host"]+
                                '</span>';

                            if(child["info_url"])
                                child["info_url"] = 
                                    '<a href="'+child['info_url']+'" '+
                                        ' qtip="Show info_url" class="icinga-icon-information icinga-icon-24">link</a>';
                            var state = child["hardstate"];
                            if(state)
                                child["hardstate"] = '<div class="icinga-status icinga-status-'+state.toLowerCase()+'" style="height:12px;text-align:center">'+state+'</div>';
                            child.cls = 'bp_serviceRow';
                            child["leaf"] = true;
                        }
                    } catch(e) {
                        AppKit.log(e);
                    }
                }
            }			
        },

        /**
         * Creates relationgroups (AND/OR/MIN) and injects them between a process and
         * its subprocesses
         */
        buildRelationGroups : function(objToAdd,_name,all) {
            // Traverse down this branch and build filtergroups from the bottom to the top
            if(objToAdd["children"])
                for(var i=0;i<objToAdd["children"].length;i++)
                    this.buildRelationGroups(objToAdd["children"][i]);
            if(!objToAdd["operator"])
                return true;
            var relname = objToAdd["operator"]; 
            if(relname == 'of') {		
                relname = "Min "+objToAdd["min_ok"]+" OK";
            }
            // Create the child
            objToAdd["children"] =  [{
                display_name: relname, 
                children: objToAdd["children"],
                iconCls: 'icinga-icon-package',
                hardstate: '',
                ignoreFilter: true,
                uiProvider:'col'
            }]

        }

    })
});