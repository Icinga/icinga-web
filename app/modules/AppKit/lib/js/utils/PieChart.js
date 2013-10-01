;(function() {
    "use static";
    Ext.ns('AppKit.util');

    var sortPieDataSet = function(l, r) {
        return r.value - l.value;
    }

    AppKit.util.PieChart = Ext.extend(Ext.BoxComponent, {

        constructor: function(cfg) {
            console.log("Create ", cfg);
            Ext.BoxComponent.prototype.constructor.apply(this, arguments);
        },

        initComponent: function(cfg) {
            Ext.BoxComponent.prototype.initComponent.apply(this, arguments);
        },

        render: function(el) {
            Ext.BoxComponent.prototype.render.apply(this, arguments);
            this.rendered = true;
            var r = Raphael(el.dom, el.getWidth(), el.getHeight());
            var values = [];
            var colors = [];

            this.transformDataSet(values, colors);
            var pie = r.piechart(el.getWidth()/2, el.getHeight()/2, 100, values, {colors: colors, init:true, stroke: '#121'});

            for (var i = 0;i < pie[0].length; i++) {
                pie[0][i].attr('stroke-width', 0.5);
                pie[0][i].attr('stroke', '#121');
            }

        },


        transformDataSet: function(values, colors) {
            var total = 0;
            this.store.each(function(el) {
                total += parseInt(el.get('COUNT'), 10) || 0;
                values.push(el.get('COUNT'));
            });
            var chartData = []
            for (var i = 0; i < values.length; i++) {
                chartData.push({
                    color: this.seriesStyles.colors[i],
                    value: values[i] / total * 100
                });
            }

            chartData = chartData.sort(sortPieDataSet);
            for (var i = 0; i < chartData.length; i++) {
                values[i] = chartData[i].value;
                colors[i] = chartData[i].color;
            }
        }

    });
    Ext.reg('icingapie', AppKit.util.PieChart);
})();

