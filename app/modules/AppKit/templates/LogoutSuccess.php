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

    $t['current'] = $ro->gen(null);
?>
<script type="text/javascript">
Ext.onReady(function() {
    var m = new AppKit.util.LogoutMachine(<?php echo json_encode($t); ?>);
    m.doLogout();
});
</script>
<h1>Bye ...</h1>
<p style="font-size: 10pt;">
    You've beed logged out successfully!
    You can go to the <a href="<?php echo $t['url']; ?>">loginpage</a>
    to authentificate again.
</p>
