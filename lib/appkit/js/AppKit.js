
var AppKit = {
		sleep : function(ms) {
			var zeit=(new Date()).getTime();
			var stoppZeit=zeit+ms;
			while((new Date()).getTime()<stoppZeit){};
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
		
		delayCall: function(iInterval, fCallback) {
			(function() {
				
				function caller() {
					fCallback();
					window.clearInterval(timer);
				};
				
				var timer = window.setInterval(caller, iInterval); 
				
			})();
		}
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
