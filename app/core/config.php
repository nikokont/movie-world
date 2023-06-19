<?php

define("WEBSITE_TITLE", 'MY SHOP');

//db credentials
define('DB_NAME', "movie_world");
define('DB_USER', "root");
define('DB_PASS', "");
define('DB_HOST', "localhost");


//when uploaded to server we want this to be false
define("DEBUG", true);


if (DEBUG) {
    //php settings to tell it how to run
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}
