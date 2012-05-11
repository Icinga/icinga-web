// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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

function CronkTrigger (config) {

    var thisConfig = {};

    function initConfig() {
        thisConfig.objectId = false;
        thisConfig.objectName = false;
        thisConfig.objectType = false;
        thisConfig.returnVal = false;       
    };

    function setConfig (config) {
        if (config.objectId != undefined && config.objectType != undefined) {
            thisConfig.objectId = config.objectId;
            thisConfig.objectName = config.objectName;
            thisConfig.objectType = config.objectType;
            completeConfig();
        }
    };

    function completeConfig () {
        switch (thisConfig.objectType) {
            case "host":
                thisConfig.idPrefix = "servicesForHost";
                thisConfig.titlePrefix = _("Services for ");
                thisConfig.targetTemplate = "icinga-service-template";
                thisConfig.targetField = "host_object_id";
                break;
            default:
                initConfig();
                break;
        }
    };

    function createCronk () {
        if (thisConfig.objectId != false && thisConfig.objectType != false) {
            var cronk = {
                parentid: thisConfig.idPrefix + "subGridComponent",
                title: thisConfig.titlePrefix + thisConfig.objectName,
                crname: "gridProc",
                closable: true,
                params: {template: thisConfig.targetTemplate}
            };

            var filter = {};
            filter["f[" + thisConfig.targetField + "-value]"] = thisConfig.objectId;
            filter["f[" + thisConfig.targetField + "-operator]"] = 50;

            Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
        }
    };

    initConfig();
    setConfig(config);
    createCronk();
    return thisConfig.returnVal;

};