<?php
//Composer dépendence
require_once './vendor/autoload.php';
use WGE\App;


//Création du home
App::home('mySite')->path('homes/mySite/')->file('index.php');

//Création host
$home = App::getCurrentHost();
App::host( $home )->home('mySite');