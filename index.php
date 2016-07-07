<?php
require_once './vendor/autoload.php';
use WGE\App;

/*
|--------------------------------------------------------------------------
| App::home() AND App::plugin()
|--------------------------------------------------------------------------
| 
| Declare the plugin here and Man will be loaded by Web goupil Engine
|
*/
App::home('mySite')->path('homes/mySite/')->file('index.php');



/*
|--------------------------------------------------------------------------
| App::bdd()
|--------------------------------------------------------------------------
|
| Declare the database here
|
*/



/*
|--------------------------------------------------------------------------
| App::host()
|--------------------------------------------------------------------------
|
| State here the hosts is or will be used
|
*/
$home = App::getCurrentHost();
App::host( $home )->home('mySite');