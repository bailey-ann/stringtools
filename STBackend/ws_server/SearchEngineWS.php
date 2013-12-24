<?php
/*
 * Created on 21/nov/2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class SearchEngineWS {
	
	
	public $dbServer = "localhost";
	public $user = "stringto_svil";
	public $pwd = "stringto_svil1";
	public $dbName = "stringto_svil";
	
	function getLogin($username, $password){
		$conn=mysqli_connect("localhost","stringto_svil","stringto_svil1","stringto_svil");
		$query = "select * from tbl_users where username = '".$username."' and password = '".md5($password)."'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		if(count($row) > 0)
			return "true";
		else
			return "false";
	}
	
	
	
	
	function getWebUrl($name){
	    $engines = array(
	        'google'    => 'www.google.it',
	        'yahoo' => 'www.yahoo.it'
	    );
	    return isset($engines[$name]) ? $engines[$name] : "Search Engine unknown";
	}
	}
	
	ini_set("soap.wsdl_cache_enabled", "0"); 
	$server= new SoapServer("search_engine.wsdl");
	$server->setClass("SearchEngineWS");
	$server->handle();
?>
