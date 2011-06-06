<?php
class TestValidator extends AppKitJsonValidator 
{
    public function  setValidationParameters(AgaviRequestDataHolder $r) {
        $this->validationParameters = $r;
    }

}
class AppKitJsonValidatorTest extends PHPUnit_Framework_TestCase {
   
    public function runValidator($f,$in) { 
        $req = new AgaviRequestDataHolder();
        $req->setParameter("input",$in);
       
        $exec = AgaviContext::getInstance()->getController()->createExecutionContainer();
         
        $val = $exec->getValidationManager()->createValidator(
            'TestValidator',
            array("input"), 
            array(
                "invalid_json"=>"Invalid json provided", 
                "invalid_format"=>"Invalid format"
            ),
            array(
                "format"=>$f,
                "name" => "test_filter",
                "base" => NULL,
                "export"=>"test", 
                "source" => "parameters"
            )
        );
        
        $val->setValidationParameters($req);
        
        return $val->validate();    
    }
 
    /**
    *
    * @dataProvider correctDataProvider
    **/    
    public function testCorrectValidation($f,$in) {
       $this->assertTrue($this->runValidator($f,$in),"Correct format/input validation failed");
    }
    
    /** 
    * @dataProvider invalidFormatProvider
    * @expectedException UnknownJsonFieldValidatorTypeException
    * @depends testCorrectValidation
    **/    
    public function testInvalidFormat($f,$in) {
        $this->assertFalse($this->runValidator($f,$in));
    }

    /** 
    * @dataProvider invalidInputProvider 
    * @depends testCorrectValidation
    **/    
    public function testInvalidInput($f,$in) {
        $this->assertFalse($this->runValidator($f,$in));
    }

    public function correctDataProvider() {
        return array(
            array('{"testField": "ANY"}','{"testField":"hello","blob":"TEST"}'),
            array('{"testField": "ANY"}','{"testField":["hello","world"], "blob": "TEST"}'),
            array('{"testField": ["\w+"]}','{"testField":["hello","world"], "blob": "TEST"}'),
            array('{"testField": ["\w+"]}','{"testField":[],"blob": "TEST"}'),
            array('{"testField": "/h[el]*o/"}','{"testField": "hello", "blob": "TEST"}'),
            array('
                {
                    "testField": {   
                        "test2": "ANY", 
                        "test3": {
                            "test4": [".*"],
                            "test5": "/[0-4]{2}/"
                        }
                    }
                }','{
                    "testField": {
                        "test2": "dontCare", 
                        "test3": {"test324": "tegds", "test4": ["1",2,"test"], "test5": 42}
                    }, "blob": "TEST"}'
            ),
            array('{
               "name": "ANY", 
               "year": "/^[1-2]\d{3}/", 
               "author" : {
                   "name" : "ANY",
                   "refs" : [{
                        "title" : "/Book \d/", 
                        "pageLine": [
                           {"page": "/P.\d+/","line": "/L \d+/"}
                        ]
                    }] 
               }
           }', '{
                "name" : "Me", 
                "year": 2001, 
                "author" : {
                    "name": "me",
                    "refs": [
                        {"title" : "Book 1","pageLine": [{"page": "P.4", "line" : "L 20"}]},
                        {"title" : "Book 2","pageLine": [{"page": "P 25", "line" : "L 35"}]}
                    ]
                } 
            }')

        );
    }
    public function invalidFormatProvider() {
        return array(
            array('{"testField" : "BLOB"}','{"testField": "dsdsgdgs"}'), 
            array('{"testField" : {"nested": "BLOB"}}','{"testField": {"nested": "dsdsgdgs"}}')

        );
    }
    public function invalidInputProvider() {
        return array( 
           array('{"testField": "ANY"}','{"blob": "TEST"}'), 
            array('{"testField": [".*"]}','{"testField":"hello", "blob": "TEST"}'),
            array('{"testField": "/h[el]*o/"}','{"testField": "hubbo", "blob": "TEST"}'),
            array('
                {
                    "testField": {   
                        "test2": "ANY", 
                        "test3": {
                            "test4": [".*"],
                            "test5": "/[0-4]{2}/"
                        }
                    }
                }','{
                    "testField": {
                        "test2": "dontCare", 
                        "test3": {"test324": "tegds", "test4": ["1",2,"test"], "test5": 52}
                    }, "blob": "TEST"}'
            ), 
            array('{
               "name": "ANY", 
               "year": "/^[1-2]\d{3}/", 
               "author" : {
                   "name" : "ANY",
                    "refs" : [{"title" : "/Book \d/"}] 
               }
           }', '{
                "name" : "Me", 
                "year": 2001, 
                "author" : {
                    "name": "me",
                    "refs": [{"title" : "Book 1"},{"title" : "Book z"}]
                } 
            }')
       ); 
    }
}
