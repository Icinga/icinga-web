<?php

interface IDataStoreModifier {
    public function handleArgument($name,$value);
    public function getMappedArguments();
}
