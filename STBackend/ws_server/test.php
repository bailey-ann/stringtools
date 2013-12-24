<?php
/*
 * Created on 21/nov/2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 try {
$ws = new SoapClient('http://www.stringtools.it/ws_server/search_engine.wsdl');
//print_r($gsearch->getWebUrl('google'));
print_r("A:".$ws->getLogin("napalm", ""));
} catch (SoapFault $e) {
print_r($e);
}
?>
