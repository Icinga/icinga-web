<?php

class Test_SingletonTestModel extends AgaviModel implements AgaviISingletonModel
{
	public $foo = null;
	public function setFoo($value)
	{
		$this->foo = $value;
	}
	public function getFoo()
	{
		return $this->foo;
	}
}

