<?php
$client = new SoapClient("http://localhost/icinga-web/soap/icinga.wsdl",array("trace"=>1,'features' => SOAP_SINGLE_ELEMENT_ARRAYS));
try {
	$filters = array(
		array("column"=>"HOST_NAME","relation"=>"=","value"=>"localhost"));
	$filterParam = new SoapVar($filters,SOAP_ENC_ARRAY,null,null,"soapFilter");
	$result = $client->getIcingaAPI("service",$filterParam," "," "," ",0,"authkey123412345");
} catch(Exception $e) {
	echo $e;
}
print_r($client->__getLastRequest());
print_r($client->__getLastResponse());

