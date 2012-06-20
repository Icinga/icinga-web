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
<h4>Welcome to the ICINGA Appkit Application Suite</h4>

<p>ICINGA AppKit is a application framework build on to of Agavi to implement as fast as
possible applications within the same context.</p>

<p>The first implemented module is the NETWAYSGrapher, a versatile, flash-driven plotting application to display
performance data from other applications (e.g. NAGIOS).</p>

<?php if (!$us->isAuthenticated()) { ?>
<p>We've noticed that you are not logged in, you can do this right now at the <a href="<?php echo $ro->get('appkit.login'); ?>">loginpage</a>.</p>
<?php } ?>
