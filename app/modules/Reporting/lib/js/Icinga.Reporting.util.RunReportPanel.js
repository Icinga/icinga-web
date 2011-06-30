Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.DEFAULT_JSCONTROL = {
	className : 'Icinga.Reporting.inputControl.Default'
};

Icinga.Reporting.util.RunReportPanel = Ext.extend(Ext.Panel, {
	title : _('Report details'),
	border : false,
	
	bodyStyle : {
		padding : '5px 5px 5px 5px'
	},
	
	defaults : {
		border : false
	},
	
	constructor : function(config) {
		Icinga.Reporting.util.RunReportPanel.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.RunReportPanel.superclass.initComponent.call(this);
		
		this.add({
			xtype : 'panel', 
			html : '<div style="padding: 20px;"><h3>'
				+ _('... please select a report from the left tree view')
				+ '</h3></div>'
		});
		
	},
	
	initUi : function(attributes) {
		
		this.loadingMask = new Ext.LoadMask(this.getEl());
		this.loadingMask.show();
		
		this.nodeAttributes = attributes;
		
		Ext.Ajax.request({
			url: this.parampanel_url,
			params : { uri : attributes.uri },
			success: this.parseOutput.createDelegate(this)
		});
		
	},
	
	buildFormItems : function(panel, struct) {
		Ext.iterate(struct, function(k,v) {
			var inputConfig = {};
			
			Ext.apply(v.jsControl, {
				hidden : v.PROP_INPUTCONTROL_IS_VISIBLE=="false" ? true : false,
				readonly : v.PROP_INPUTCONTROL_IS_READONLY=="true" ? true : false,
				name : v.name,
				width: 250,
				fieldLabel : v['label'],
				allowBlank : false
			});
			
			Ext.applyIf(v.jsControl, Icinga.Reporting.DEFAULT_JSCONTROL);
			
			inputConfig = v.jsControl;
			
			if (!Ext.isEmpty(inputConfig.className)) {
				var inputClass = eval('window.' + inputConfig.className);
				var inputControl = new inputClass(inputConfig);
				panel.add(inputControl);
			}
			
		}, this);
	},
	
	buildInterface : function(struct) {
		this.removeAll();
		
		this.add({
			layout : 'fit',
			html : String.format('<h1>{0}</h1>{1}', this.nodeAttributes.text, this.nodeAttributes.uri),
			border : true,
			bodyStyle : {
				margin: '2px 0px 10px 0px',
				padding: '2px'
			}
		});
		
		if (this.parameterData.length == 0) {
			this.add({
				layout: 'fit',
				html: String.format('<h4>{0}</h4><i>{1}</i>', _('No report'), _('Sorry, no report selected. Please select a report item in the tree on the left'))
			});
		} else {
			this.formPanel = this.createForm();
			
			this.buildFormItems(this.formPanel, this.parameterData);
			
			var outputSelector = new Icinga.Reporting.inputControl.OutputFormatSelector({
				name : '_output_format',
				fieldLabel : _('Output format'),
				width : 250
			});
			
			this.formPanel.add(outputSelector);
			
			this.submitButton = this.createSubmitButton(); 
			
			this.formPanel.add({
				type : 'container',
				width : 356,
				border : false,
				style : {
					background : 'transparent',
					padding : '10px 10px 10px 10px',
					margin: '10px'
				},
				items: this.submitButton
			});
			
			this.add(this.formPanel);
		}
		this.doLayout();
	},
	
	parseOutput : function(response, options) {
		this.parameterData = Ext.util.JSON.decode(response.responseText);
		
		this.loadingMask.hide();
		delete this.loadingMask;
		
		this.buildInterface(this.parameterData);
	},
	
	createForm : function() {
		var panel = new Ext.form.FormPanel({
			bodyStyle: { background: 'transparent' }
		});
		
		this.form = panel.getForm();
		
		var baseUrl = this.creator_url;
		var uri = this.nodeAttributes.uri;
		
		this.form.on('beforeaction', function(form, action) {
			values = form.getFieldValues();
			var useUrl = baseUrl.replace(/OUTPUT_TYPE/, values['_output_format']);
			action.options.url = String.format('{0}?uri={1}', useUrl, uri);
		});
		
		return panel;
	},
	
	createSubmitButton : function() {
		var submit = new Ext.Button({
			xtype : 'button',
			iconCls : 'icinga-icon-report-run',
			iconAlign : 'top',
			text : _('Run report!'),
			style: 'margin: 0 0px 0 auto'
		});
		
		this.formAction = new Ext.form.Action.JSONSubmit(this.form, { params : {} });
		
		submit.on('click', function(b, e) {
			this.form.doAction(this.formAction, {
				clientValidation : true 
			});
		}, this);
		
		return submit;
	}
});