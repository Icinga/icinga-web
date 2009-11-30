/**
 * Declare our namespace
 */
Ext.ns('AppKit');

/**
 * Global application singleton
 */
AppKit.Ext = function() {
	
	var pub = {};
	
	var ridCount = 0;
	
	var ridChars = (
		"0,1,2,3,4,5,6,7,8,9"
		+ ",a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z"
		+ "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z"
	).split(',');
	
	var isReady = false;
	
	var growlStack;
	
	var appStateData;
	
	var events = {
		'statedata' : true,
		'isready' : true
	};
	
	var initEnvironment = function() {
		// Default image for ExtJS
		Ext.BLANK_IMAGE_URL = 'images/ajax/s.gif';
		
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
	};
	
	var createPopupBox = function(title, text) {
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
			
			growlStack.alignTo(document, 'tr-tr', [-18, 10]);
			
			var params = [];
			var config = {
				waitTime: 2
			};
			
			if (arguments.length > 2) {
				if (Ext.isArray(arguments[2])) {
					params = arguments[2];
				}
				
				if (Ext.isObject(arguments[3])) {
					Ext.apply(config, arguments[3]);
					
					if (config.params && Ext.isArray(config.params)) {
						params = config.params;
					}
				}
				
				if (params.length == 0 && !Ext.isObject(arguments[3])) {
					params = Array.prototype.slice.call(arguments, 2);
				}
			}
			
			var fa = [format];
			
			Ext.apply(params, fa);
			
			// var t = Array.concat(fa, params);
			
			var string = String.format.apply(String, params); 
			
			var ele = Ext.DomHelper.append(growlStack, {html: createPopupBox(title, string)}, true);
			ele.slideIn('t').pause(config.waitTime).ghost("t", {remove:true});
		},
		
		genRandomId : function(sPrefix, iLength) {
			var newDate = new Date;
			var min=0;
		    var max=ridChars.length-1;
		    var id="";
		    
		    for(var i=0; i<(iLength || 12);i++) {
		          id += ridChars[ Math.floor(Math.random()*(max - min + 1) + min) ];
		    }
		    
		    if (sPrefix && !(sPrefix.indexOf('-')+1 == sPrefix.length)) {
		    	sPrefix += '-';
		    }
		    
		    return String.format('{0}{1}-{2}-{3}', sPrefix, newDate.getTime(), id, ridCount++);
		},
		
		changeLocation : function(sUrl) {
			if (window.location.replace) {
				window.location.replace(sUrl);
			}
			else {
				window.location.href = sUrl;
			}
			
			return true;
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
