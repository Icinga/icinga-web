Ext.ns('Icinga', 'Icinga.DEFAULTS');

AppKit.on('appkit-ready', function() {

Icinga.DEFAULTS = {};

Icinga.DEFAULTS.OBJECT_TYPES = {
	host: {
		oid: 1,
		iconClass: 'icinga-object-host'
	},
	
	service: {
		oid: 2,
		iconClass: 'icinga-object-service'
	},
	
	hostgroup: {
		oid: 3,
		iconClass: 'icinga-object-hostgroup'
	},
	
	servicegroup: {
		oid: 4,
		iconClass: 'icinga-object-servicegroup'
	}
};

Icinga.DEFAULTS.STATUS_DATA = {
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
	}
};

Icinga.StatusData = (function() {
	
	var pub = Ext.apply({}, Icinga.DEFAULTS.STATUS_DATA);
	
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
		
		var t = '';
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
		
		wrapElement : function(type, statusid) {
			return elementTemplate.apply(elementWrapper(type, statusid));
		},
		
		wrapText : function(type, statusid) {
			return textTemplate.apply(elementWrapper(type, statusid));
		},
		
		renderServiceStatus : function(value, metaData, record, rowIndex, colIndex, store) {
			return Icinga.StatusData.wrapElement('service', value);
		},
		
		renderHostStatus : function(value, metaData, record, rowIndex, colIndex, store) {
			return Icinga.StatusData.wrapElement('host', value);
		},
		
		renderSwitch : function(value, metaData, record, rowIndex, colIndex, store) {
			var t = 'type';
			return Icinga.StatusData.wrapElement(record.data[t], value);
		}
		
	});
	
	return pub;
	
})();

Icinga.ObjectData = (function() {
	
});

}, window, { single: true });
