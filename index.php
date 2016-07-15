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
App::home('mySite')->path('homes/mySite/')->file('mySite.php');


/*
|--------------------------------------------------------------------------
| App::bdd()
|--------------------------------------------------------------------------
|
| Declare the database here
|
*/
//App::bdd('nom_de_ma_bdd')->host('localhost')->user('root')->password('')->database('mybase');


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