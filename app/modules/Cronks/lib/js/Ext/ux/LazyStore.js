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
/* 
 * Ext.ux.Store that delays loading 
 */
(function() {
    "use strict";
    Ext.ns("Ext.ux").LazyStore = Ext.extend(Ext.data.Store,{
       constructor: function() {
           this.loadTask = new Ext.util.DelayedTask(Ext.data.Store.prototype.load);
           return Ext.data.Store.prototype.constructor.apply(this,arguments);
       },
       
       load: function() {
           this.loadTask.delay(200,null,this,arguments);
           return true;
       }
    });
    Ext.ns("Ext.ux").LazyGroupingStore = Ext.extend(Ext.data.GroupingStore,{

       load: function() {
           if(!this.loadTask)
               this.loadTask = new Ext.util.DelayedTask(Ext.data.Store.prototype.load);
           this.loadTask.delay(200,null,this,arguments);
           return true;
       }
    });
})();


