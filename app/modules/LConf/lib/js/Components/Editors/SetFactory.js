Ext.ns("LConf.Editors").SetFactory = new function() {
    var baseRoute = ""
    this.setBaseRoute = function(route) {
        baseRoute = route;
    }
    this.getBaseRoute = function() {
        return baseRoute;
    }

    this.create = function(src,urls) {
        var propertyStore = new Ext.data.JsonStore({
            autoDestroy:false,
            url: String.format(urls.ldapmetaprovider),
            baseParams: {field:src}
            // Metadata is provided by the server
        })

        return Ext.extend(Ext.form.ComboBox,{
            triggerAction: 'all',
            lazyRender:true,
            displayField: 'entry',
            valueField: 'entry',
            mode:'remote',
            store: propertyStore,
            pageSize: 25,
            tpl: '<tpl for="."><div style="padding-left:25px" class="icinga-icon-{cl} x-combo-list-item">{entry}</div></tpl>',
            enableKeyEvents: true,
            initList: function() {
                var _comboScope = this;
                Ext.form.ComboBox.prototype.initList.apply(this,arguments);
                this.view.collectData = function(recordArray) {
                    var available = _comboScope.getValue().split(",");
                    for(var i=0;i<available.length;i++) {
                        available[i] = Ext.util.Format.trim(available[i]);
                    }

                    for(var i=0;i<recordArray.length;i++) {
                        var record = recordArray[i];
                        if(available.indexOf(record.get("entry")) > -1)
                            record.data['cl'] = 'delete';
                        else
                            record.data['cl'] = 'add';

                    }
                    return Ext.DataView.prototype.collectData.apply(this,arguments);
                }
            },
            listeners:  {
                beforeselect: function(_form,rec,row) {
                    var node = _form.view.getNode(row);
                    var old = _form.getValue();
                    var newVal = rec.get('entry');
                    row = Ext.get(_form.view.getNode(row));
                    // check whether to remove or to add an element
                    if(rec.data['cl'] == 'delete') {
                        var splitted = old.split(",");
                        var newSet = [];
                        for(var i=0;i<splitted.length;i++) {
                            if(Ext.util.Format.trim(splitted[i]) != newVal)
                                newSet.push(splitted[i]);
                        }
                        _form.setValue(newSet.join(","))
                        rec.data['cl'] = 'add';
                        row.replaceClass('icinga-icon-delete','icinga-icon-add');
                    } else {
                        if(old)
                            _form.setValue(old+","+newVal);
                        else
                            _form.setValue(newVal);
                        rec.data['cl'] = 'delete';
                        row.replaceClass('icinga-icon-add','icinga-icon-delete');
                    }

                    return false;
                },
                keypress: function(combo,e) {
                    combo.collapse();
                }
            }
        });
    }
}
