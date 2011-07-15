Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ContentResourcePanel = Ext.extend(Ext.Panel, {
	
	title : _('Content resource'),
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			bodyStyle : {
				padding : '5px 5px 5px 5px'
			},
			tbar : [{
				text : _('Save to disk'),
				iconCls : 'icinga-icon-disk',
				handler : this.processDownload,
				scope : this
			}, {
				text : _('Preview'),
				iconCls : 'icinga-icon-eye',
				handler : this.processPreview,
				scope : this
			}],
			
			plugins : [
				new Ext.ux.plugins.ContainerMask ({msg : _('Please be patient, fetching resource ...'), masked : false })
			],
			
			tpl : new Ext.XTemplate(
				'<tpl for="data">'
				+ '<div class="simple-content-box">'
				+ '<h1>{label}</h1>'
				+ '</div>'
				
				+ '<dl>'
				
				+ '<dt>{[ _("URI") ]}</dt>'
				+ '<dd>{uriString}</dd>'
				
				+ '<dt>{[ _("Parent") ]}</dt>'
				+ '<dd>{PROP_PARENT_FOLDER}</dd>'
				
				+ '<dt>{[ _("Resource name") ]}</dt>'
				+ '<dd>{name}</dd>'
				
				+ '<dt>{[ _("Creation date") ]}</dt>'
				+ '<dd>{crdate}</dd>'
				
				+ '<dt>{[ _("Type") ]}</dt>'
				+ '<dd>{wsType}</dd>'
				
				+ '<dt>{[ _("Jasper Type") ]}</dt>'
				+ '<dd>{PROP_RESOURCE_TYPE}</dd>'
				
				+ '</dl>'
				
				+ '<tpl if="has_attachment==true">'
				+ '<div class="simple-content-box">'
				+ '<h1>{[ _("Attachment") ]}</h1>'
				+ '</div>'
				+ '<dl>'
				
				+ '<dt>{[ _("Mime type") ]}</dt>'
				+ '<dd>{content_type}</dd>'
				
				+ '<dt>{[ _("Size") ]}</dt>'
				+ '<dd>{[ fm.fileSize(values.content_length) ]}  ({content_length} bytes)</dd>'
				
				+ '</dl>'
				+ '</tpl>'
				
				+ '</tpl>', {
					compiled : true
				}
			)
		});
		
		Icinga.Reporting.util.ContentResourcePanel.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.ContentResourcePanel.superclass.initComponent.call(this);
		
		this.meta_url = this.resource_url;
		this.content_url = this.meta_url.replace(/\/meta$/, '/content');
		
		this.setToolbarEnabled(false);
	},
	
	setToolbarEnabled : function(bool, pos) {
		if (Ext.isEmpty(bool)) {
			bool = true;
		}
		var i = 0;
		this.getTopToolbar().items.eachKey(function(key, item) {
			if (!Ext.isEmpty(pos)) {
				if (pos == ++i) {
					item.setDisabled(!bool);
				}
			}
			else {
				item.setDisabled(!bool);
			}
		});
	},
	
	processMetaLoad : function(node) {
		this.showMask();
		this.setToolbarEnabled(false);
		Ext.Ajax.request({
			url : this.meta_url,
			params : {
				uri : node.attributes.uri
			},
			success : function(response, option) {
				try {
					var data = Ext.util.JSON.decode(response.responseText);
					this.update(data);
					
					if (data.data.download_allowed == true) {
						this.setToolbarEnabled(true, 1);
					}
					
					if (data.data.preview_allowed == true) {
						this.setToolbarEnabled(true, 2);
					}
					
					this.data = data;
				}
				catch(e) {
					AppKit.log(e);
				}
			},
			callback : function(options, success, response) {
				this.hideMask();
			},
			scope : this
		});
	},
	
	processNodeClick : function(node, e) {
		this.node = node;
		this.processMetaLoad(node);
	},
	
	processDownload : function(b, e) {
		
	},
	
	processPreview : function(b, e) {
		var tabs = this.parentCmp.parentCmp;
		tabs.setActiveTab(tabs.add({
			title : this.data.data.name,
			closable : true,
			bodyCfg : {
				tag : 'iframe',
				src : Ext.urlAppend(this.content_url, Ext.urlEncode({
					inline : 1,
					uri : this.node.attributes.uri
				}))
			}
		}));
		
	}
});