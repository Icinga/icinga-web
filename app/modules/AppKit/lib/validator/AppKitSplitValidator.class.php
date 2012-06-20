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


/**
 * Validator that takes a string and exports it to an array by splitting it
 * by a specified char.
 *
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class AppKitSplitValidator extends AgaviValidator {

    protected function validate() {
        $context = $this->getContext();
        $argument = $this->getArgument();
        $data = $this->getData($argument);
        $splitChar = $this->getParameter("split",";");
        $splitted = array();
        if(is_array($data)) // ignore if already splitted
           $splitted = $data;
        else
            $splitted = explode($splitChar,$data);
        $this->export($splitted);
        return true;
    }

}