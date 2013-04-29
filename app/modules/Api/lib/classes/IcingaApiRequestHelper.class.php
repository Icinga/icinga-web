<?php
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

/**
 * Class that encapsulates helpers for the ApiDataRequestModel
 * All methods must return a IcingaApiRequestDescriptor
 */
interface IcingaApiRequestHelper {


    public function getService($serviceName,$hostName,$instance = null);
    public function getServicesInServiceGroup($servicegroup,$instance = null,&$count = null,$offset = 0,$limit = -1);
    public function getServicesForHost($host,$instance = null,&$count = null,$offset = 0,$limit = -1);
    public function getHistoryForServices(array $services,$instance = null,&$count = null,$offset = 0,$limit = -1);

    public function getHostsInHostGroup($hostgroup,$instance = null,&$count = null,$offset = 0,$limit = -1);
    public function getHost($hostName,$instance = null);
    public function getHistoryForHosts(array $hostNames,$instance = null,&$count = null,$offset = 0,$limit = -1);

    public function getCommentById($id);
    public function getCommentsForHosts(array $host,$instance = null,&$count = null,$offset = 0,$limit = -1);
    public function getCommentsForServices(array $services,$instance = null,&$count = null,$offset = 0,$limit = -1);

    public function getCustomVariablesByName($name,$instance = null,&$count = null,$offset = 0,$limit = -1);
    public function getCustomVariablesForHosts(array $hosts,$name = null,$instance = null,&$count = null,$offset = 0,$limit = -1);
    public function getCustomVariablesForServices(array $services,$name = null,$instance = null,&$count = null,$offset = 0,$limit = -1);

    public function getHostgroup($name,$instance = null);
    public function getServicegroup($name,$instance = null);

    public function getInstance($name);


}
