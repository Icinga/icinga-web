Ext.ns('Cronk.grid');

Cronk.grid.InfoIconColumnRenderer = new (function () {
	
	var buildIcon = function(iconCls, title) {
		return Ext.DomHelper.createDom({
			tag : 'div',
			cls : 'x-icinga-info-icon ' + iconCls,
			qtip : title
		});
	}
	
	var buildIconFrame = function(data, element) {
		// element.removeClass('icinga-icon-throbber');
		
		if (data.check_type == 'passive') {
			element.appendChild(buildIcon('icinga-icon-info-passive', _('Accepting passive only')));
		} else if (data.check_type == 'disabled') {
			element.appendChild(buildIcon('icinga-icon-info-disabled', _('Check is disabled')));
		}
		
		if (data.in_downtime == true) {
			element.appendChild(buildIcon('icinga-icon-info-downtime', _('Object in downtime')));
		}
		
		if (data.is_flapping == true) {
			element.appendChild(buildIcon('icinga-icon-info-flapping', _('Object is flapping')));
		}
		
		if (data.notification_enabled == false) {
			element.appendChild(buildIcon('icinga-icon-info-notifications-disabled', _('Notifications for this object are disabled')));
		}
		
		if (data.problem_acknowledged == true) {
			element.appendChild(buildIcon('icinga-icon-info-problem-acknowledged', _('Problem has been acknowledged')));
		}
		
	}
	
	var updateContent = function(data, type, columns) {
		if (data.success == true) {
			Ext.iterate(data.rows, function(oid, obj, arry) {
				var id = String.format('object-info-icon-{0}-{1}', type, oid);
				if (columns.contains(id)) {
					var element = columns.item(columns.indexOf(id));
					buildIconFrame(obj, element);
				}
				
			}, this);
		}
	}
	
	var loadInfoData = function() {
		var columns = this.grid.getEl().select("div.object-info-icon-cell");
		if (columns.getCount()) {
			
			var type = "";
			var oids = [];
			var re = new RegExp(/^[\w-]+-(\d+)-(\d+)$/);
			var test = []
			columns.each(function(el, c, idx) {
				test = re.exec(el.id);
				oids.push(test[2]);
			}, this);
			
			type = test[1];
			
			Ext.Ajax.request({
				url : AppKit.util.Config.get('path') + '/modules/appkit/dispatch',
				params : {
					module : 'Cronks',
					action : 'Provider.ObjectInfoIcons',
					params : Ext.encode({
						type : type,
						oids : oids.join(',')
					})
				},
				success : function(response, opts) {
					//try {
						var data = Ext.decode(response.responseText);
						updateContent(data, type, columns);
					//} catch (e) {
						//AppKit.log('Could not decode object info data ' + e);
					//}
					
				},
				scope : this
			})
		}
	}
	
	this.init = function(grid, c) {
		this.grid = grid;
		this.grid.getStore().on('load', loadInfoData, this);
	}
	
	this.infoColumn = function(cfg) {
        return function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.DomHelper.markup({
            	tag : 'div',
            	cls : 'object-info-icon-cell', // icinga-icon-throbber icon-16
            	id : 'object-info-icon-' + cfg.type + '-' + value
            });
        };
    };
	
})();