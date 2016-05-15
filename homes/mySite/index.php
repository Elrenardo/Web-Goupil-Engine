<?php
use WGE\App;

//Template
App::template('index')->path('tpl/index.html');

//Ajout d'une route
App::route('/')->template('index');