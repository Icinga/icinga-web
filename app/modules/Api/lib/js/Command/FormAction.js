Ext.ns('Icinga.Api.Command');

Icinga.Api.Command.FormAction = Ext.extend(Ext.form.Action, {
	
	clientValidation : true,
	
	targets : [],
	
	command : {},
	
	constructor : function(form, options) {
		Icinga.Api.Command.FormAction.superclass.constructor.call(this, form, options);
		
		if (Ext.isEmpty(options.targets) === false) {
			this.setTargets(options.targets);
		}
		
		if (Ext.isEmpty(options.command) === false) {
			this.setCommand(options.command);
		}
		
		this.commandSender = new Icinga.Api.Command.Sender({
			autoReset : true
		});
		
		this.commandSender.on('success', this.success.createDelegate(this));
		this.commandSender.on('error', this.failure.createDelegate(this));
	},
	
	setTargets : function(targets) {
		this.targets = targets
	},
	
	setCommand : function(command) {
		this.command = command;
	},
	
	run : function() {
		var o = this.options;
		if(o.clientValidation === false || this.form.isValid()) {
			var data = this.form.getFieldValues();
			
			this.commandSender.send({
				command : this.command.definition,
				targets : this.targets,
				data : data
			});
			
		} else if (o.clientValidation !== false){ // client validation failed
            this.failureType = Ext.form.Action.CLIENT_INVALID;
            this.form.afterAction(this, false);
        }
	},
	
	success : function() {
		this.form.afterAction(this, true);
	},
	
	failure : function() {
		this.failureType = Ext.form.Action.SERVER_INVALID;
        this.form.afterAction(this, false);
	}
});