<?php

class ApiRestrictedCommandException extends AppKitException {};
class ApiAuthorisationFailedException extends AppKitException {};
abstract class BaseConsoleConnection {
    abstract public function exec(Api_Console_ConsoleCommandModel $cmd);
    abstract public function __construct(array $settings = array());
}
