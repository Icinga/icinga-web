
var AppKit = {
	
	emptyContainer : function(sId) {
		$('#' + sId).html('');
	},
		
	parseJson : function (jsonString) {
	
		try {
			var data = YAHOO.lang.JSON.parse(jsonString);
			return data;
		}
		catch(e) {
			alert('Invalid JSON data: ' + e);
		}
		
		return false;
	
	},
	
	ajaxLastResponse: false,
	
	ajaxFormRequest: function(sUrl, sFormId, hSuccess) {
		var eForm = $('#' + sFormId);
		if (eForm) {
			
			YAHOO.util.Connect.setForm(document.getElementById(sFormId), false);
			
			var callback = {
				success : function(o) {
				
					if (hSuccess) {
						hSuccess(o);
					}
					else {
						// HM?
					}
				
				}
			}
			
			var cObj = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback); 
			
			
		}
	},
	
	ajaxHtmlRequest : function(sUrl, sTargetId, bAppend, successHandler) {
		var ele = $('#' + sTargetId);
		var sReturn = true;
		
		sUrl = sUrl.replace(/&amp;/g, '&');
		
		var loader = new Image();
		loader.src = "images/ajax/circle-ball.gif";
		
		sUrl = sUrl.replace(/&amp;/g, '&');
		
		var callback = {
			customevents: {
				onStart : function(o) {
					if (successHandler) {
					if (bAppend !== true) {
						ele.html(loader);
					}
					}
				}
			},
			
			success : function(o){
				AppKit.ajaxLastResponse = o;
				
				if (successHandler) {
					successHandler(o);
				}
				
				else {
					if (bAppend == true) {
						ele.html(ele.html() + o.responseText);
					}
					else {
						ele.html(o.responseText);
					}
				}
			}
		}
		
		var oRequest = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
		
		return true
	},
		
	initAutoCompleteField : function(oConfig) {
		var oDS = new YAHOO.util.XHRDataSource(oConfig.url);
		oDS.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;
		oDS.responseSchema = {
				resultsList: "ResultSet.Result",
				fields : ["value","key"]
		}; 
		
		var oAC = new YAHOO.widget.AutoComplete(oConfig.input, oConfig.container, oDS); 
		oAC.resultTypeList = false; 
		oAC.queryDelay = .4;
		oAC.minQueryLength = 0;
		oAC.prehighlightClassName = "yui-ac-prehighlight"; 
		oAC.useShadow = true;
		oAC.maxResultsDisplayed = 20;
		oAC.animSpeed = 0.1;
		
		var myHiddenField = YAHOO.util.Dom.get(oConfig.hidden);
	    var myHandler = function(sType, aArgs) {
	        var myAC = aArgs[0]; // reference back to the AC instance
	        var elLI = aArgs[1]; // reference to the selected LI element
	        var oData = aArgs[2]; // object literal of selected item's result data
	        
	        // update hidden form field with the selected item's ID
	        myHiddenField.value = oData.key;
	    };
	    oAC.itemSelectEvent.subscribe(myHandler);

	 	// Adding a click hander for activating the field
	    YAHOO.util.Event.addListener(oAC.getInputEl(), "focus", function() {
	    	oAC.getInputEl().focus();
	    	setTimeout(function() {
				oAC.sendQuery("");
	    	});
	    });
	    
	    return ({
	    	oAC: oAC,
	    	oDS: oDS
	    });
	    
	},
	
	
	lastPanel: null,
	
	AppKitPanelHolder : function() {},
	
	deleteYuiPanel : function(sId) {
		if (AppKit.AppKitPanelHolder[sId]) {
			delete AppKit.AppKitPanelHolder[sId];
			return true;
		}
		
		return false;
	},
	
		
	
	yuiPanelFromId : function(sId, oConfig) {
		
		var oDefault = { 
				width:"320px", 
				visible:false, 
				constraintoviewport:true
		};
		
		if (oConfig !== null) {
			jQuery.extend(oDefault, oConfig);
		}
		
		YAHOO.util.Event.onContentReady(sId, function() {
			if (!AppKit.AppKitPanelHolder[sId]) {
				AppKit.AppKitPanelHolder[sId] = 
					new YAHOO.widget.Panel(sId, oDefault);
				
				AppKit.AppKitPanelHolder[sId].render();
				
				AppKit.lastPanel = AppKit.AppKitPanelHolder[sId];
			}	
		});
		
		if (AppKit.AppKitPanelHolder[sId]) {
			return AppKit.AppKitPanelHolder[sId];
		}
	},
	
	yuiAjaxPopup : function (sUrl, sTargetId, sPanelId, oConfig) {
		
		AppKit.ajaxHtmlRequest(sUrl, null, false, function(o) {
		
			function pAjaxHide() {
				$('#' + sPanelId).remove()
				$('#' + sTargetId).html('');
				pAjax = null;
				resizs = null;
			}
			
			var oDefault = { 
					width:"120px",
					height:"120px",
					visible:false, 
					draggable:false, 
					close:true 
			};
				
			if (oConfig !== null) {
				jQuery.extend(oDefault, oConfig);
			}
			
			var pAjax = new YAHOO.widget.Panel(sPanelId, oConfig);
			pAjax.setBody(o.responseText);
			
			if (oConfig.customFooter) {
				pAjax.setFooter(oConfig.customFooter);
			}
			else {
				pAjax.setFooter('&#160;');
			}
			
			if (oConfig.customHeader) {
				pAjax.setHeader(oConfig.customHeader);
			}
			
			pAjax.hideEvent.subscribe(pAjaxHide);
			
			pAjax.render(sTargetId);
			
			if (!oDefault.noResize) {
				var resize = new YAHOO.util.Resize(sPanelId, { 
					handles: ['br'],
					autoRatio: false,
					minWidth: 300,
					minHeight: 100,
					status: false
				});
				
				resize.on('resize', function(args) { 
					var panelHeight = args.height; 
					this.cfg.setProperty("height", panelHeight + "px"); 
					}, pAjax, true); 
				
				resize.on('startResize', function(args) {
	
				    if (this.cfg.getProperty("constraintoviewport")) {
				        var D = YAHOO.util.Dom;
	
				        var clientRegion = D.getClientRegion();
				        var elRegion = D.getRegion(this.element);
	
				        resize.set("maxWidth", clientRegion.right - elRegion.left - YAHOO.widget.Overlay.VIEWPORT_OFFSET);
				        resize.set("maxHeight", clientRegion.bottom - elRegion.top - YAHOO.widget.Overlay.VIEWPORT_OFFSET);
				    } else {
				        resize.set("maxWidth", null);
				        resize.set("maxHeight", null);
				    }
				}, pAjax, true);
			}
			
			AppKit.sleep(200);
			
			pAjax.show();
			
			AppKit.lastPanel = pAjax;
			
			if (oConfig.hReadyPanel) {
				oConfig.hReadyPanel(pAjax, o);
			}
		
		});
		
	},
	
	prettyPrintForId : function(sId) {
		YAHOO.util.Event.onContentReady(sId, function() {
			prettyPrint();
		});	
	},
	
	sleep : function(ms) {
		var zeit=(new Date()).getTime();
		var stoppZeit=zeit+ms;
		while((new Date()).getTime()<stoppZeit){};
	},
	
	yuiCalPopup : function(sTarget, sPrefix, oConfigDialog, oConfigCal) {
		
		function cleanContainer() {
			AppKit.emptyContainer(sTarget);
		}
		
		function hClose() {
			this.hide();
		}
		
		function hDummy() {
			alert('Set handler not defined!');
		}
		
		var oDefaultDialog = { 
				visible:false, 
				draggable:true, 
				close:true,
				autofillheight: 'body',
				buttons:[ 
				         {text:"Close", handler: hClose}
				]
		};
		
		if (oConfigDialog !== null) {
			jQuery.extend(oDefaultDialog, oConfigDialog);
		}
		
		var oDefaultCal = {
			iframe:false
		};
		
		if (oConfigCal !== null) {
			jQuery.extend(oDefaultCal, oConfigCal);
		}
		
		dialog = new YAHOO.widget.Dialog(sPrefix + '-cdialog', oDefaultDialog);
		dialog.hideEvent.subscribe(cleanContainer);
		
		if (oDefaultDialog.customHeader) {
			dialog.setHeader(oConfigDialog.customHeader);
		}
		
		var cal_id = sPrefix + '-ccontrol';
		
		dialog.setBody('<div id="' + cal_id + '"></div>');
		
		if (oDefaultDialog.customFooter) {
			dialog.setFooter(oConfigDialog.customFooter);
		}

		dialog.render(sTarget);
		
		var cal = new YAHOO.widget.Calendar(cal_id, oDefaultCal);
		
		cal.renderEvent.subscribe(function() {
			dialog.fireEvent('changeContent');
		});
		
		cal.render();
		
		return {
			dialog: dialog,
			calendar: cal
		};
		
	},
	
	dump : function(s) {
		dump(s);
	},
	
	fixedInteger: function(i, iLength) {
		var s = new String(i);
		if (s.length < iLength) {
			for(var z=0; z<=(iLength-s.length); z++) {
				s = "0" + s;
			}
		}
		
		return s;
	},
	
	urlSeperator: '&amp;',
	
	urlParser: function(oUrl) {
		if (typeof oUrl == 'object') {
			var out = new String();
			for (var key in oUrl) {
				if (out.length) {
					out += AppKit.urlSeperator;
				}
				out += key + "=" + AppKit.urlEscape(oUrl[key]);
			}
			return out;
		}
		
		return '';
	},
	
	urlEscape: function(item) {
		var string = escape(item);
		string = string.replace(/\+/g, '%2B');
		return string;
	},
	
	appendParams: function(sUrl, oParams) {
		if (sUrl.indexOf('?') == -1) {
			sUrl += '?';
		}
		else {
			sUrl += AppKit.urlSeperator;
		}
		
		sUrl += AppKit.urlParser(oParams);
		
		return sUrl;
	},
	
	Cookie: new AppKitCookie('AppKitJS'),
	
	getPageYOffset: function() {
		var value = 0;
		if (window.pageYOffset) {
			value = parseInt(window.pageYOffset);
		}
		else if (document.body && document.body.scrollTop) {
			value = parseInt(document.body.scrollTop);
		}
		return value;
	},
	
	loadingPanel: function(sImage) {
		
		var panel = new YAHOO.widget.Panel("wait", { 
			width:"240px", 
			fixedcenter:true, 
			close:false, 
			draggable:false, 
			zindex:4,
			modal:true,
			visible:false
		});
		
		
		
		panel.setHeader('Loading ...');
		panel.setBody('<img src="' + sImage + '" />');
		
		panel.render(document.body);
		
		panel.show();
	},
	
	delayCall: function(iInterval, fCallback) {
		(function() {
			
			function caller() {
				fCallback();
				window.clearInterval(timer);
			};
			
			var timer = window.setInterval(caller, iInterval); 
			
		})();
	},
	
	messagePopup: function(sText, hYes, cOpt) {
		(function() {
			
			function handleYes() {
				if (hYes) {
					hYes();
				}
				
				this.hide();
			}
			
			var dOpt = {
					width: '320px',
					fixedcenter: true,
					visible: false,
					draggable: false,
					close: true,
					text: sText,
					icon: YAHOO.widget.SimpleDialog.ICON_INFO,
					constraintoviewport: true,
					buttons: [
					 { text:"Ok", handler:handleYes, isDefault:true },
					]
			};
			
			if (cOpt) {
				jQuery.extend(dOpt, cOpt);
			}
			
			var oDialog = new YAHOO.widget.SimpleDialog("cDialog", dOpt);
			oDialog.render(document.body);
			
			oDialog.show();
			
		})();
	},
	
	confirmPopup: function(sText, hYes, hNo, cOpt) {
		
		(function() {
			
			function handleYes() {
				if (hYes) {
					hYes();
				}
				this.hide();
			}
			
			function handleNo() {
				if (hNo) {
					hNo();
				}
				this.hide();
			}
			
			var dOpt = {
					width: '320px',
					fixedcenter: true,
					visible: false,
					draggable: false,
					close: true,
					text: sText,
					icon: YAHOO.widget.SimpleDialog.ICON_INFO,
					constraintoviewport: true,
					buttons: [
					 { text:"Yes", handler:handleYes, isDefault:true },
					 { text:"No",  handler:handleNo }
					]
			};
			
			if (cOpt) {
				jQuery.extend(dOpt, cOpt);
			}
			
			var oDialog = new YAHOO.widget.SimpleDialog("cDialog", dOpt);
			oDialog.render(document.body);
			
			oDialog.show();
			
		})();
		
	}
};


// Cookie object
function AppKitCookie(name) {
	
	this.name 	= name;
	this.obj	= YAHOO.util.Cookie;
	
	this.set = function(key, val) {
		return this.obj.setSub(this.name, key, val);
	};
	
	this.get = function(key) {
		return this.obj.getSub(this.name, key);
	};
	
	this.getHash = function() {
		return this.obj.getHash(this.name);
	};
	
	this.setHash = function(oHash) {
		return this.obj.setSubs(this.name, oHash);
	};
	
	this.remove = function(key) {
		return this.obj.removeSub(this.name, key);
	}
	
	this.deleteCookie = function() {
		return this.obj.remove(this.name);
	};
	
};

// Object container
function AppKitObjectContainer() {
	
	this.objects = {};
	
	this.addObject = function(name, o) {
		this.objects[name] = o;
		return true;
	};
	
	this.rmObject = function(name) {
		if (this.objects[name]) {
			delete this.objects[name];
			return true;
		}
		
		return false;
	};
	
	this.getObject = function(name) {
		if (this.objects[name]) {
			return this.objects[name];
		}
		
		return false;
	};
	
};

// Some prototypes
Date.prototype.toIso = function() {
	
	var string = this.getFullYear()
	+ "-" + AppKit.fixedInteger((this.getMonth()+1), 2)
	+ "-" + AppKit.fixedInteger(this.getDate(), 2)
	+ " " + AppKit.fixedInteger(this.getHours(), 2)
	+ ":" + AppKit.fixedInteger(this.getMinutes(), 2)
	+ ":" + AppKit.fixedInteger(this.getSeconds(), 2);
	
	return string;
};

Date.prototype.toUnixEpoch = function() {
	return (this.getTime() / 1000);
};
