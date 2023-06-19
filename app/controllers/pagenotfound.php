<?php

class Pagenotfound extends Controller
{

    
    public function index()
    {

        $data['page-title'] = "Home";
        $this->view("client/pagenotfound", $data);
    }
}
