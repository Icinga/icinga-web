Ext.ns('Icinga.Api.Command');

Icinga.Api.Command.Facade = (new (Ext.extend(Object, {
	
	commandSender : null,
	
	constructor : function() {
		this.commandSender = new Icinga.Api.Command.Sender({
			autoReset : true
		});
	},
	
	sendCommand : function(o) {
		Ext.copyTo(this.commandSender, o, [
	        'command', 'targets', 'data'
		]);
		
		this.commandSender.send();
	}
	
}))());