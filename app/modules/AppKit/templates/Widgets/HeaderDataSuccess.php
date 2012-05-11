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

    $webpath = $t['web_path'];
    $type = $rd->getParameter('type', 'javascript');
    $imports = isset($t['imports']) && is_array($t['imports']) ? $t['imports'] : array();
    $includes = isset($t['includes']) && is_array($t['includes']) ? $t['includes'] : array();
    
    switch($type) {
        case 'javascript':
            foreach($includes as $_ => $include) {
                echo
<<<INCLUDE
<script type="text/javascript" src="$include"></script>
INCLUDE
;
            }
                
            break;
        case 'css':
            echo '<style type="text/css">';
            foreach($imports as $_ => $import) {
                $import = $webpath . $import;
                echo
<<<IMPORT
@import url("$import");
IMPORT
;
            }
            echo '</style>';
            
            foreach($includes as $_ => $include) {
                echo
<<<INCLUDE
<link href="$include" rel="stylesheet" type="text/css">
INCLUDE
;                       
            }
                
            break;
    }
?>
