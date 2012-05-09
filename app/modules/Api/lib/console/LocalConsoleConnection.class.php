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


class LocalConsoleConnection extends BaseConsoleConnection {

    public function exec(Api_Console_ConsoleCommandModel $cmd) {
        $cmdString = $cmd->getCommandString();
        $this->checkFileExistence($cmd);
        
        exec($cmdString,$out,$ret);
        
        $cmd->setReturnCode($ret);
        $cmd->setOutput($out);
    }

    protected function checkFileExistence(Api_Console_ConsoleCommandModel $cmd) {
        if ($cmd->getStdin()) {
            if (!file_exists($cmd->getStdin())) {
                throw new AppKitException("File ".$cmd->getStdin()." does not exist");
            }
        }

        if ($cmd->getPipedCmd()) {
            $this->checkFileExistence($cmd->getPipedCmd());
        }
    }

    public function __construct(array $settings = array()) {
        // No setup needed
    }

}
