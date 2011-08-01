Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.DEFAULT_JSCONTROL = {
	className : 'Icinga.Reporting.inputControl.Default'
};

Icinga.Reporting.util.RunReportPanel = Ext.extend(Icinga.Reporting.abstract.ApplicationWindow, {
	title : _('Report details'),
	border : false,
	
	bodyStyle : {
		padding : '5px 5px 5px 5px'
	},
	
	defaults : {
		border : false
	},
	
	mask_text : _('Please be patient, generating report . . .'),
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			bbar : [{
				
			}],
			
			tbar : [{
				text : _('Run report'),
				iconCls : 'icinga-icon-report-run',
				handler: this.runReport,
				scope : this
			}, {
				text : _('Preview'),
				iconCls : 'icinga-icon-report-preview',
				handler : this.previewReport,
				scope : this
			}]
		});
		
		Icinga.Reporting.util.RunReportPanel.superclass.constructor.call(this, config);
		this.downloadUrl = String.format('{0}/modules/reporting/provider/reportdata', AppKit.util.Config.getBaseUrl());
	},
	
	initComponent : function() {
		Icinga.Reporting.util.RunReportPanel.superclass.initComponent.call(this);
		
		this.add({
			xtype : 'panel', 
			html : '<div style="padding: 20px;"><h3>'
				+ _('... please select a report from the left tree view')
				+ '</h3></div>'
		});
		
		
		this.setToolbarEnabled(false);
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
	
	buildInterface : function(struct) {
		this.removeAll();
		
		this.setTitle(_(String.format('Report details for {0}', this.nodeAttributes.text)));
		
		this.add({
			layout : 'fit',
			html : String.format('<h1>{0}</h1>{1}', this.nodeAttributes.text, this.nodeAttributes.uri),
			border : false,
			cls : 'simple-content-box'
		});
		
		if (this.parameterData.length == 0) {
			this.add({
				layout: 'fit',
				html: String.format('<h4>{0}</h4><i>{1}</i>', _('No report'), _('Sorry, no report selected. Please select a report item in the tree on the left'))
			});
		} else {
			this.formPanel = this.createForm();
			
			var builder = new Icinga.Reporting.util.InputControlBuilder({
				target : this.formPanel,
				controlStruct : this.parameterData
			});
			
			builder.applyToTarget();
			
			var outputSelector = new Icinga.Reporting.inputControl.OutputFormatSelector({
				name : '_output_format',
				fieldLabel : _('Output format'),
				width : 250
			});
			
			this.formPanel.add(outputSelector);
			
			this.messagePanel = new Ext.Container({
				border : false,
				width : 356,
				style : {
					padding : '10px',
					margin : '5px',
					background : 'transparent'
				}
			});
			
			this.formPanel.add(this.messagePanel);
			
			this.add(this.formPanel);
		}
		
		this.doLayout();
		
		this.setToolbarEnabled();
	},
	
	addMessage : function(html, cls) {
		this.messagePanel.removeAll();
		this.messagePanel.add({
			xtype : 'container',
			html : {
				tag : 'span',
				html : html,
				cls : cls
			}
		});
		this.messagePanel.doLayout();
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
		
		this.formAction = new Ext.form.Action.JSONSubmit(this.form, {
			params : {},
			scope: this,
			success : function(form, action) {
				this.setToolbarEnabled();
				this.hideMask();
			},
			failure : function(form, action) {
				this.setToolbarEnabled();
				this.hideMask();
				
				if (action.failureType == "server") {
					var data = Ext.util.JSON.decode(action.response.responseText);
					if (!Ext.isEmpty(data.errors.message)) {
						AppKit.notifyMessage(_('Jasperserver error'), data.errors.message);
						this.addMessage(data.errors.message, 'icinga-message-error');
					}
					else {
						var msg = _('Some general error, please examine jasperserver logs');
						AppKit.notifyMessage(_('Jasperserver error'), msg);
						this.addMessage(msg, 'icinga-message-error');
					}
				}
			}
		});
		
		var baseUrl = this.creator_url;
		var uri = this.nodeAttributes.uri;
		
		this.form.on('beforeaction', function(form, action) {
			values = form.getFieldValues();
			AppKit.log(action);
			
			var format = values['_output_format']
			
			/**
			 * Hook for changing the output type
			 * without changing our form
			 */
			if (!Ext.isEmpty(this.formAction.options.overwrite_format)) {
				format = this.formAction.options.overwrite_format;
				delete this.formAction.options.overwrite_format;
			}
			
			var useUrl = baseUrl.replace(/OUTPUT_TYPE/, format);
			
			action.options.url = String.format('{0}?uri={1}', useUrl, uri);
		}, this);
		
		return panel;
	},
	
	submitForm : function(o) {
		this.setToolbarEnabled(false);
		this.showMask();
		this.messagePanel.removeAll();
		
		if (Ext.isObject(o)) {
		
			if (Ext.isFunction(o.success)) {
			
				var successHandler = function(form, action) {
					o.success.call(o.scope || this, form, action);
				}
				
				this.form.on('actioncomplete', successHandler, this, { single : true });
				
				/*
				 * We need to remove the handler when failed because the handler is
				 * persistent and maybe three success handler would be called after
				 * three failures for only one success ?!
				 */
				this.form.on('actionfailed', function(form, action) {
					this.form.un('actioncomplete', successHandler, this);
				}, this, { single : true });
				
			}
			
			
		
		}
		
		this.form.doAction(this.formAction);
	},
	
	startEmbeddedDownload : function() {
		var dlUrl = this.downloadUrl;
		var eId = 'icinga-reporting-dl-iframe';
		Ext.DomHelper.append(Ext.getBody(), {
			tag : 'iframe',
			id : eId,
			src : dlUrl
		});
		
		(function() {
			Ext.get(eId).remove();
		}).defer(2000);
	},
	
	addEmbeddedReportPreview : function() {
		var tabs = this.parentCmp.parentCmp;
		var previewTab = tabs.add({
			xtype : 'panel',
			title : this.nodeAttributes.text,
			iconCls : 'icinga-icon-eye',
			closable : true,
			bodyCfg : {
				tag : 'iframe',
				src : String.format('{0}?inline=1', this.downloadUrl)
			}
		});
		tabs.setActiveTab(previewTab);
	},
	
	runReport : function(b, e) {
		this.submitForm({
			success : function(form, action) {
				this.startEmbeddedDownload();
			},
			scope : this
		});
	},
	
	previewReport : function(b, e) {
		this.formAction.options.overwrite_format = 'html';
		this.submitForm({
			success : function(form, action) {
				this.addEmbeddedReportPreview();
			},
			scope : this
		});
	}
});