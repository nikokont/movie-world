<?php

class User_movies extends Controller
{


    public function index($id)
    {
        $User = $this->load_model('User');
        $user_data = $User->check_login();

        if (is_object($user_data)) {
            $data['logged-user'] = $user_data;
        }

        $id = (int)$id; //user id

        $Movie = $this->load_model('Movie'); //get the model
        //----
        //get the movies of selected user
        $getUserMovies = $Movie->getUserMovies($id);

        //get the movies template of selected user
        $data['user-movies'] = $Movie->makeMovieCard($getUserMovies);

        //count the movies of selected user
        $data['user-movies-number'] = $Movie->countUserMovies($id);

        //get the info of selectes user to display his username

        $data['user-info'] = $User->getUser($id);

        $data['page-title'] = "User movies";

        $this->view("client/user-movies", $data);
    }
}
