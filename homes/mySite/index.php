<?php
use WGE\App;

//Template principal du site
App::template('master_layout')->path('web/master_layout.html');


//Ajout de la route page d'accueil
App::template('tpl_home')->path('web/home.html');
App::route('/')->template('tpl_home')->name('home');