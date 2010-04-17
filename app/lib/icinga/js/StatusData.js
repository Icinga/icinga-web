Ext.ns('Icinga');

AppKit.on('appkit-ready', function() {
	
Icinga.StatusData = (function() {
	
	var pub = {};
	
	var elementTemplate = new Ext.Template('<div class="icinga-status {cls}"><span>{text}</span></div>');
	elementTemplate.compile();
	
	var elementWrapper = function(type, statusid) {
		var c = '';
		if (type == 'host') {
			c = pub.hoststatusClass[statusid];
		}
		else if (type == 'service') {
			c = pub.servicestatusClass[statusid];
		}
		
		var t = '???';
		if (type == 'host') {
			t = pub.hoststatusText[statusid];
		}
		else if (type == 'service') {
			t = pub.servicestatusText[statusid];
		}
		
		return { cls: c, text: t };
	};
	
	var textTemplate = new Ext.Template('<span class="icinga-status-text {cls}">{text}</span>');
	textTemplate.compile();
	
	Ext.apply(pub, {		
		TYPE_HOST : 'host',
		TYPE_SERVICE : 'service',
		
		HOST_UP : 0,
		HOST_DOWN: 1,
		HOST_UNREACHABLE: 2,
		
		hoststatusText : {
			0: _('UP'),
			1: _('DOWN'),
			2: _('UNREACHABLE')
		},
		
		hoststatusClass : {
			0: 'icinga-status-up',
			1: 'icinga-status-down',
			2: 'icinga-status-unreachable'
		},
		
		SERVICE_OK : 0,
		SERVICE_WARNING : 1,
		SERVICE_CRITICAL : 2,
		SERVICE_UNKNOWN : 3,
		
		servicestatusText : {
			0: _('OK'),
			1: _('WARNING'),
			2: _('CRITICAL'),
			3: _('UNKNOWN')
		},
		
		servicestatusClass : {
			0: 'icinga-status-ok',
			1: 'icinga-status-warning',
			2: 'icinga-status-critical',
			3: 'icinga-status-unknown'
		},
		
		wrapElement : function(type, statusid) {
			return elementTemplate.apply(elementWrapper(type, statusid));
		},
		
		wrapText : function(type, statusid) {
			return textTemplate.apply(elementWrapper(type, statusid));
		}
		
	});
	
	return pub;
	
})();

});