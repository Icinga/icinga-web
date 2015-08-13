<?php
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

/**
* Interface for Console Commands
* TODO: Currently this holds the command as well as it's current state,
*       this could be outsourced to a commandresult class/interface
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
interface IcingaConsoleCommandInterface {
    public function setCommand($cmd);
    public function addArgument($value, $key=null);
    public function stdinFile($file = null);
    public function stdoutFile($file = null,$append = false);
    public function stderrFile($file = null,$append = false);
    public function pipeCmd(IcingaConsoleCommandInterface $cmd = null);
    public function setOutput($string);
    public function setReturnCode($code);
    public function setConnection($conn);
    public function setHost($host);
    public function getHost();
    public function getStdin();
    public function getStderr();
    public function getStdout();
    public function getPipedCmd();
    public function getCommand();
    public function getArguments();
    public function getConnection();
    public function getOutput();
    public function getReturnCode() ;
    public function initialize(AgaviContext $context, array $parameters = array());
    public function getCommandString();
    public function isValid($throwOnError = false, &$err = null);




}
