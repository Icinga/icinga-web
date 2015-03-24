// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
// All rights reserved.
//
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

;(function() {
    "use static";
    Ext.ns('AppKit.util');

    var sortPieDataSet = function(l, r) {
        return r.value - l.value;
    }

    /**
     * Graphing component for rendering PieCharts via RaphaelJS
     *
     * @type {*}    Configuration object, see ExtJS charts
     */
    AppKit.util.PieChart = Ext.extend(Ext.BoxComponent, {

        /**
         * Render the pie chart with the given dataset and styles
         *
         * @param {Ext.Element} el        The element to render the chart to
         */
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

        /**
         * Order and normalize the dataset in order to display it correctly with gRaphael
         *
         * gRaphael internally orders the dataset descending, but not the colors, so we have
         * to do this before in order to keep the colorset consistent
         *
         * @param {Array} values        The relative sizes of the pie chart (will be modified)
         * @param {Array} colors        The colors of the pie chart         (will be modified)
         */
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

