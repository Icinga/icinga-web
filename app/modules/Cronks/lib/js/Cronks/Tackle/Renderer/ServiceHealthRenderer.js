/*global Ext: false, Icinga: false, _: false */

Ext.ns('Icinga.Cronks.Tackle.Renderer');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Renderer.ServiceHealthRenderer = function (value) {
        var id = Ext.id();
        var _this = this;

        var render = function (nrOfTry) {
            nrOfTry = nrOfTry || 1;
            if(!Ext.get(id) ) {
                if(nrOfTry < 4)
                    render.defer(100,this,[nrOfTry+1]);
                return false;
            }

            var cmp = new Ext.BoxComponent({
                layout: 'fit',
                tpl: new Ext.XTemplate(
                    '<tpl>',
                        "<div style='border:1px solid #dedede;height:15px'>",
                            "<div qtip='{SERVICES_0} (of {COUNT_SERVICES_TOTAL}) services without open problems' style='width:{PERC_SERVICES_0}%;background-color:green;height:15px;float:left;'></div>",
                            "<div qtip='{SERVICES_1} (of {COUNT_SERVICES_TOTAL}) services with state warning (open problems)' style='width:{PERC_SERVICES_1}%;background-color:yellow;height:15px;float:left;'></div>",
                            "<div qtip='{SERVICES_2} (of {COUNT_SERVICES_TOTAL}) services with state critical (open problems)' style='width:{PERC_SERVICES_2}%;background-color:red;height:15px;float:left;'></div>",
                            "<div qtip='{SERVICES_3} (of {COUNT_SERVICES_TOTAL}) services with state unknown (open problems)' style='width:{PERC_SERVICES_3}%;background-color:#ffee00;height:15px;float:left'></div>",
                        '</div>',
                     '</tpl>'),
                renderTo: id
            });

            _this.summaryStore.addListener("load", function (v, r) {
                var obj = {
                    SERVICES_0: 0,
                    SERVICES_1: 0,
                    SERVICES_2: 0,
                    SERVICES_3: 0,
                    SERVICES_99: 0,
                    PERC_SERVICES_0: 0,
                    PERC_SERVICES_1: 0,
                    PERC_SERVICES_2: 0,
                    PERC_SERVICES_3: 0,
                    PERC_SERVICES_99: 0,
                    COUNT_SERVICES_TOTAL: 0
                };

                _this.summaryStore.filter("HOST_ID", value);
                _this.summaryStore.each(function (r) {
                    obj["SERVICES_" + r.get('SERVICE_CURRENT_PROBLEM_STATE')] += parseInt(r.get('SERVICE_STATE_COUNT'), 10);
                    obj.COUNT_SERVICES_TOTAL += parseInt(r.get('SERVICE_STATE_COUNT'), 10);
                });

                for (var idx in obj) {
                    if (Ext.isPrimitive(obj[idx])) {
                        if (idx === "PERC_SERVICES_TOTAL") {
                            continue;
                        }
                        obj["PERC_" + idx] = parseInt(obj[idx] * 100 / obj.COUNT_SERVICES_TOTAL, 10);
                    }
                }

                cmp.update(obj);
            }, _this, {
                single: true
            });
        }
        
        render.defer(100);

        return '<div id="' + id + '"></div>';
    };
})();