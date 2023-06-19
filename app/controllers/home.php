<?php


class Home extends Controller
{

    public function index()
    {
        $User = $this->load_model('User');
        $user_data = $User->check_login(); // this function returns a value (check i user class). So 
        //we need to store the function in a variable. The variable will return false or will have an array inside.

        $data['logged-user'] = NULL;

        if (is_object($user_data)) {
            $data['logged-user'] = $user_data;
            //print_r($data['logged-user']);
        }

        $db = Database::getInstance();

        $Movie = $this->load_model('Movie');

        //get movies 
        $getMovies = $Movie->getAll();


        $data['movies-number'] = $Movie->countMovies();
        $data['movies'] = $Movie->makeMovieCard($getMovies);

        $data['page-title'] = "Movie World";

        $this->view("client/index", $data);
    }
}
