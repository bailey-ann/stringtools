<?php


$basePath=dirname(__FILE__);
$frameworkPath='../prado-3.2.2.r3297/framework/prado.php';
$assetsPath = $basePath."/assets";

if(!is_writable($assetsPath))
	die("Please make sure that the directory $assetsPath is writable by Web server process.");

require_once($frameworkPath);
//include_once("analyticstracking.php") ;

$application=new TApplication;
$application->run();

?>