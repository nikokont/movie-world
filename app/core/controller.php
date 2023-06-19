<?php

class Controller
{

    //function for loading a view in a controller
    public function view($path, $data = []) /**/
    {
        //if the view exists
        if (file_exists("../app/views/" . $path . ".php")) {

            //include it
            include "../app/views/" . $path . ".php";
        }else{
            include "../app/views/404.php";
        }
    }


//function for loading a model in a controller
    public function load_model($model) /**/
    {
        //if the model exists
        if (file_exists("../app/models/" . strtolower($model) . ".class.php")) {

            //include it
            include "../app/models/" . strtolower($model) . ".class.php";
            return $m=new $model(); //we instantiate the model class
        }else{
            return false ;
        }
    }
}


/** -> we use the data property because we may need some data in the page. For example if the page is homepage we may need a page title page etc. but because we wont always
 * have data we asign it to an empty array so we dont get undefined error message in case we dont pass data. */
