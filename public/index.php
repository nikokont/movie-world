<?php

session_start();
include "../app/init.php";

//date_default_timezone_set('Athens/Greece');


// we create dynamic routs for assets (check read me file)
//print_r($_SERVER);

// the things we need from server:

// [REQUEST_SCHEME] ->http
// [SERVER_NAME] -> localhost (here)
// [PHP_SELF] -> /* project-name */public/index.php


//we must make the root path
$root = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
// this will give us the root path -> http://localhost/* project-name */public/index.php , but we dont need index.php in the end so we must remove it.
$root = str_replace("index.php", "", $root);
$root = str_replace("public/", "", $root);

define('ROOT', $root); // we create a constant for root path and this also with the below assets constant, will be used in the vies to include their assets.
define('ASSETS', $root . "assets"); //root path with assets at the end -> http://localhost/* project-name */public/assets
$app = new App(); //we can do this because this comes from app.php which contains App class, which in in app.php which is included in init.php
