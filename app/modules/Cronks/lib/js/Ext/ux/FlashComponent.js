// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

(function() {
    "use strict";

    Ext.ns('Ext.chart', 'YAHOO.widget');

    /**
     * @class Ext.chart.PieChart
     * @overrides Ext.chart.PieChart
     * @namespace Ext.chart
     * @author Markus Frosch <markus.frosch@netways.de>
     * @getId
     */
    Ext.override(Ext.chart.PieChart, {
        /*
          Replacing getId with a new version to give the YUI swf
          an id that he wants and allows
        */
        getId: function() {
            return this.id || (this.id = "yuiswf" + (++Ext.Component.AUTO_ID));
        }
    });

    /**
     * @class YAHOO.widget.SWF
     * @extends Ext.FlashEventProxy
     * @namespace YAHOO.widget
     * @author Markus Frosch <markus.frosch@netways.de>
     *
     * A proxy object to call Ext.FlashEventProxy
     * from a YUI flash component
     */
    YAHOO.widget.SWF = Ext.FlashEventProxy;
    YAHOO.widget.SWF.eventHandler = function(id, e) {
        this.onEvent(id, e);
    };
}());
