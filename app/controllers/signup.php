<?php


class Signup extends Controller
{


    public function index()
    {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $user = $this->load_model("user");
            $user->signup($_POST);
        }

        $data['page-title'] = "Signup";

        $this->view("client/signup", $data);
    }
}
