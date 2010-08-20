/* 
 * Global error handler for icinga-web
 * .
 */

Ext.ns("AppKit.errorHandler");
(function() {
	AppKit.errorHandler = new function() {
		var errorMsg = function(msg,file,line) {
			this.msg = 'No message available';
			this.file = 'No file available';
			this.line = 'No line available';
			this.stack = 'No stacktrace available'
			this.time = new Date().toLocaleString();
			try {
				this.msg = msg;
				this.file = file;
				this.line = line;

			//	this.stack = printStackTrace({e:msg,guess:false})
			} catch(e) {}
		};

		var errorReport = function() {
			this.text = ''
			this.send = function() {}
			this.show = function() {}
		}

		var occuredErrors = [];
		var suspended = false;
		var showErrors = true;
		var handleError = function(msg,file,line) {
			AppKit.log("!");
			var curError = new errorMsg(msg,file,line);
			occuredErrors.push(curError);

			if(showErrors) {
				updateErrorDisplay();
			}

		};
		var bugReportField = null;
		var updateErrorDisplay = function() {
			if(!bugReportField)
				setupErrorDisplay();
			else {
				bugReportField.setText(occuredErrors.length);
				Ext.getCmp('menu-navigation').doLayout();
			}
			
		}

		var setupErrorDisplay = function() {

			var elem = Ext.getCmp('menu-navigation');
			bugReportField = new Ext.Button({
				text: occuredErrors.length,
				iconCls: 'icinga-icon-bug',
				handler: AppKit.errorHandler.showErrorMessageInfoBox
			})
			elem.addItem(bugReportField);
			elem.doLayout();
			
		}

		window.onerror = handleError;

		return {
			clearErrors : function() {
				occuredErrors = [];
				updateErrorDisplay();
			},
			getErrors: function() {
				return occuredErrors;
			},
			setError: function(msg,file,line) {
				this.handleError(msg,file,line);
			},

			suspend: function() {
				window.onerror = function() {};
				suspended = true;
			},

			resume: function() {
				window.onerror = handleError;
				suspended = false;
			},

			isSuspended: function() {
				return suspended
			},

			showErrorMessageInfoBox: function() {
				var data = [];
				var i=0;
				Ext.each(occuredErrors,function(error) {
					data.push([i++,error.msg,error.file,error.line,error.time]);
				})
				var dview = new Ext.DataView({
					store:new Ext.data.ArrayStore({
						fields: ['id','msg','file','line','time'],
						idIndex: 0,
						data: data,
						autoDestroy: true
					}),
					tpl: new Ext.XTemplate(
						'<tpl for=".">',
							'<div class="icinga-bugBox">',
								'<b>Message</b>: {msg}<br/>',
								'<b>File</b>: {file}<br/>',
								'<b>Line</b>: {line}<br/>',
								'<b>Occured</b>: {time}',
							'</div>',
						'</tpl>')
				});
				var box = new Ext.Window({
					modal:true,
					height:500,
					width:700,
					title: _('Bug report'),
					layout:'auto',
					items: [{
						padding:5,
						html:'<div class="icinga-icon-bug-32" style="padding-left:35px;padding-top:2px;height:32px;overflow:visible"><h2>'+_('Icinga bug report')+'</h2></div>'+
							'<br/>'+_('The following '+occuredErrors.length+' error(s) occured, sorry for that:')
					},{
						layout:'auto',
						xtype:'panel',
						collapsible:true,
						height:300,
						autoScroll:true,
						padding:5,
						items:dview
					}],
					buttons: [{
						text: _('Send report to admin'),
						iconCls: 'icinga-icon-application-form',
						handler: function() {new errorReport().send();},
						scope:this
					},{
						text: _('Create report for dev.icinga.org'),
						iconCls: 'icinga-icon-information',
						handler: function() {new errorReport().show();},
						scope:this
					},{
						text: _('Clear errors'),
						iconCls: 'icinga-icon-delete',
						handler: AppKit.errorHandler.clearErrors,
						scope:this
					},{
						text: _('Close'),
						iconCls: 'icinga-icon-cancel'
					}]
				}).show(document.body);
			}
		}

	}


})();
