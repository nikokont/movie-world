<?php



class App
{
    //the controller
    protected $controller = "home";
    //the method
    protected $method = "index";

    //the parameters
    protected $params;

    public function __construct()
    {

        // constuctor is the first thing that will run when we initialize this class(we do this in index.php) 

        // we need to get the url from the parseurl function below
        $url = $this->parseURL();


        /* the controller file located in controllers folder,  
         * must have the same name as the first itam of the url ($url[0])
         * so if this controller exists
        */
        if (file_exists("../app/controllers/" . strtolower($url[0]) . ".php")) {
            //change the default controller (home) to the selected controller
            $this->controller = strtolower($url[0]);

            // now since we have set it we dont need it anymore so we unset it
            unset($url[0]);
        }

        //here we require the file of the controller (????)
        require "../app/controllers/" .  $this->controller . ".php";
        $this->controller = new $this->controller;


        //now we check for method (which will be in array position 1 of url after the controller)
        if (isset($url[1])) {
            $url[1] = strtolower($url[1]);
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = (count($url) > 0) ? $url : ["home"];

        //tell the program to run function and method
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL()
    {
        //if user types a base url we will get an undefined index
        $url = isset($_GET['url']) ? $_GET['url'] : "home";

        //the above comes from url so it is not safe.. We need to sanitize it (filter_var)
        return explode("/", filter_var(trim($url, "/"), FILTER_SANITIZE_URL));
        //trim to remove any extra "/" in the end, because the way it is setup if user puts extra / in the end of url, will return empty array position
    }
}
