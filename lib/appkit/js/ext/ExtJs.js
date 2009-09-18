/**
 * Declare our namespace
 */
Ext.ns('AppKit');

/**
 * Global application singleton
 */
AppKit.Ext = function() {
	
	var pub = {},
	isReady = false,
	growlStack,
	appStateData,
	events = {
		'statedata' : true,
		'isready' : true
	},
	
	initEnvironment = function() {
		// Default image for ExtJS
		Ext.BLANK_IMAGE_URL = '/images/ajax/s.gif';
		
		// Enable quicktips
		Ext.QuickTips.init();
		
		// Try to restore the application state
		if (appStateData) {
			Ext.state.Manager.setProvider(new Ext.state.SessionProvider({
				state: appStateData || {} 
			}));
		}
		
		isReady = true;
		
		AppKit.Ext.fireEvent('isready');
	},
	
	createPopupBox = function(title, text) {
		return ['<div class="ext-msg-message">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', title, '</h3>', text, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
	};
	
	// Extending Observable to use event system
	pub = Ext.extend(Ext.util.Observable, {
		constructor : function() {
			this.listeners = {};
			this.addEvents(events);
			
			pub.superclass.constructor.call(this);
			
			// If the state is ready initialize the application
			this.on('statedata', initEnvironment, this, { single : true });
		}
	});
	
	// Our public interface
	Ext.override(pub, {
		
		bogusHandler : function(oBtn, e) {
			AppKit.Ext.Message('Button clicked ...', 'button {0} was clicked ...', oBtn.getItemId());
		},
		
		notifyMessage : function(title, format) {
			if (!growlStack) {
				growlStack = Ext.DomHelper.insertFirst(document.body, {id:'ext-msg-stack'}, true);
			}
			
			growlStack.alignTo(document, 'tr-tr', [-18, 0]);
			var string = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
			
			var ele = Ext.DomHelper.append(growlStack, {html: createPopupBox(title, string)}, true);
			ele.slideIn('t').pause(2).ghost("t", {remove:true});
		},
		
		createCronk : function(config) {
			var params = config.params || {};
			var crname = config.crname;
			var url = config.loaderUrl;
			
			if (config.htmlid) {
				params.htmlid = config.htmlid;
				config.id = config.htmlid;
			}
			else {
				params.htmlid = AppKit.genRandomId('cronk-');
				config.id = params.htmlid; 
			}
			
			// Prepare the real request params
			var rParams = new Object();
			for (var i in params) {
				rParams['p['+ i + ']'] = params[i];
			}
			
			// Remove useless stuff from the config
			Ext.each(new Array('params', 'crname', 'htmlid', 'loaderUrl'), function(key, index, ary) {
				delete config[key];
			});
			
			// Create the panel
			// @todo: make this better!!!!!!!! (UHÄHH)
			if (config.xtype == 'portlet') {
				panel = new Ext.ux.Portlet(config);
			}
			else {
				panel = new Ext.Panel(config);
			}
			
			// Modify the updater if the panel is rendered
			panel.on('render', function(panel) {
				panel.getUpdater().setDefaultUrl({
					url: url + crname,
					scripts: true,
					params: rParams
				});
				
				panel.getUpdater().refresh();
			});
			
			return panel;
		},
		
		/**
		 * Set the application state. After that call the init method
		 */
		setAppState : function(json) {
			appStateData = json;
			this.fireEvent('statedata', this, json);
		},
		
		forceInit : function() {
			if (isReady !== true) {
				this.un('statedata', initEnvironment, this);
				initEnvironment();
				this.notifyMessage('init', 'init forced!');
			}
		}
	});
	
	// returning a singleton instance
	return (new pub());
}();

/*
 * Test data
 */
AppKit.Ext.bogusMarkup = [
'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam',
'nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,', 
'sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.', 
' Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor', 
'sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam',
'nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed',
'diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.',
'Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.'
].join(' ');

/*
 * Overwrite the application init
 */
Ext.onReady(function() { AppKit.Ext.forceInit.defer(200, AppKit.Ext); });
