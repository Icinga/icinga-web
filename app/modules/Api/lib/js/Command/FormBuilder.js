Ext.ns('Icinga.Api.Command');

Icinga.Api.Command.FormBuilder = Ext.extend(Object, {
    constructor : function() {
    	Icinga.Api.Command.FormBuilder.superclass.constructor.call(this);
    },
    
    build : function(command, options) {
    	if (Ext.isString(command)) {
    		command = Icinga.Api.Command.Facade.getCommand(command);
    	}
    	
    	if (Ext.isObject(command) === false) {
    		throw("Got no command object");
    	}
    	
    	var subClass = this.getSubClassName(command.definition);
    	
    	if (!(subClass in Icinga.Api.Command.Type)) {
    		subClass = "Generic";
    	}
    	
    	var cClass = Icinga.Api.Command.Type[subClass];
    	
    	options = options || {};
    	options.command = command;
    	
    	var cObject = new cClass(options);
    	
    	return cObject;
    },
    
    getSubClassName : function(commandName) {
    	return commandName.split('_').map(function(v) {
    		return v.charAt(0).toUpperCase() + v.substr(1).toLowerCase();
    	}).join('');
    }
});