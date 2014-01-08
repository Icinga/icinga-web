// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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

Ext.ns("Cronk.grid");

(function () {

    "use strict";
    
    /**
    *   Checkbox selectionmodel that remembers object ids and offers better
    *   pagination
    *
    **/
    Cronk.grid.ObjectSelectionModel = Ext.extend(Ext.grid.CheckboxSelectionModel, {
        selectedMonitoringObjects: new Ext.util.MixedCollection(),
        
        constructor: function() {
            Ext.grid.CheckboxSelectionModel.prototype.constructor.apply(this, arguments);
            this.addListener("selectionchange", this.persistSelectedObjectIds, this);
        },
       
        syncWithPage: function() {
            var records = [];
            this.grid.getStore().each(function(displayedObject) {
                this.selectedMonitoringObjects.each(function(monitoringObject) {
                    for (var attribute in monitoringObject) {
                        if (displayedObject.data[attribute] == monitoringObject[attribute]) {
                            continue;
                        }
                        return true;
                    }
                    records.push(displayedObject);
                    return false;
                }, this);
            }, this);
            this.clearSelections();
            this.selectRecords(records);
        },
     
        persistSelectedObjectIds: function() {
            var isObjectId = /_object_id$/i;
            this.selectedMonitoringObjects.clear();
            this.selections.each(function(monitoringObject) {
                var selectedObject = {};
                for (var attribute in monitoringObject.data) {
                    if (isObjectId.test(attribute)) {
                        selectedObject[attribute] = monitoringObject.data[attribute];
                    }
                }
                this.selectedMonitoringObjects.add(selectedObject);
            }, this);
        }
    });
})();
