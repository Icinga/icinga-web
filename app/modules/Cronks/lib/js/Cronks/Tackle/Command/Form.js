Ext.ns('Icinga.Cronks.Tackle.Command');

Icinga.Cronks.Tackle.Command.Form = Ext.extend(Ext.Panel, {
	title : _('Commands'),
	
	formBuilder : null,
	record : {},
	autoScroll : true,
	
	constructor : function(config) {
        Ext.apply(this,config);
		Icinga.Cronks.Tackle.Command.Form.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.Command.Form.superclass.initComponent.call(this);
		
		this.formBuilder = new Icinga.Api.Command.FormBuilder();
	},
	
	setRecord : function(record) {
		this.record = record;
	},
	
	rebuildFormForCommand : function(commandName) {
		var title = String.format(_('Command: {0}'), commandName);
		this.setTitle(title);
		
		this.removeAll();
		
		this.form = this.formBuilder.build(commandName, {
            renderSubmit: this.standalone,
            targets: Ext.isArray(this.target) ? this.target : [this.target]
		});
		
		this.add(this.form);
		
		this.doLayout();
	}
});
