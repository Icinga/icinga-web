<?php
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
?>
<div style="text-align:center;width:480px;background-color:#dedede;;height:75px;border:1px solid #cecece;-moz-border-radius:5px;-webkit-border-radius:5px;margin-top:15%;margin:auto;padding:2em;-moz-box-shadow:2px 2px 2px #989898;-webkit-box-shadow:2px 2px 2px #989898">
<h1>You do not have sufficient credentials to access this page</h1>
<p>
    Click <a href="<?php echo $ro->gen('modules.appkit.logout',array('logout' => 1));?>">here</a> to log out
</p>

</div>
