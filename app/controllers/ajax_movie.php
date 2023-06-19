
<?php


class Ajax_movie extends Controller
{
    public function index()
    {
        if ($_POST) {
            $data = (object)$_POST; //we need the $_POST here beacauuse we are also sending an image.
            $files = $_FILES;
        } else {
            $data = file_get_contents("php://input"); //allows us to read raw data from the request body..
            $data = (json_decode($data)); // decode json into a PHP object
        }


        if (is_object($data) && isset($data->data_type)) { //even though here we get data as object from ajax, we check again if indeed is an object


            $db = Database::newInstance();
            $movie = $this->load_model('Movie');





            if ($data->data_type == 'get_movies') {

                $movies = $movie->getAll();
                $arr['data'] = $movie->makeMovieCard($movies);
                $arr['count-movies'] = $movie->countMovies();
                $arr['data_type'] = "get_movies";
                echo json_encode($arr);

                //----------------------------------------------------------------------------------------------------------------------------- 

            } else if ($data->data_type == 'add_movie') {

                $check = $movie->create($data, $files);

                if (isset($_SESSION['error']) && $_SESSION['error'] != "") {
                    $arr['message'] = $_SESSION['error'];
                    $_SESSION['error'] = ""; 
                    $arr['message_type'] = "error";
                    $arr['data'] = "";
                    $arr['data_type'] = "add_movie";
                    echo json_encode($arr);
                    return;
                } else {

                    $arr['message'] = "Movie was posted successfully!";
                    $arr['message_type'] = "success";
                    $arr['data_type'] = "add_movie";
                    $movies = $movie->getAll();
                    $numOfMovies = $movie->countMovies();
                    $arr['data'] = $movie->makeMovieCard($movies);
                    echo json_encode($arr);
                }



                //----------------------------------------------------------------------------------------------------------------------------- 
            } else if ($data->data_type == 'vote_for_movie') {


                $check = $movie->movieVote($data);



                if (isset($_SESSION['error']) && $_SESSION['error'] != "") {
                    $arr['message'] = $_SESSION['error'];
                    $_SESSION['error'] = "";
                    $arr['message_type'] = "error";
                    $arr['data'] = "";
                    $arr['data_type'] = "vote_for_movie";
                    echo json_encode($arr);
                    return;
                } else {

                    $arr['message'] = "Your Vote Was Submitted!";
                    $arr['message_type'] = "success";
                    if ($data->user_id) {
                        $movies = $movie->getUserMovies($data->user_id);
                        $numOfMovies = $movie->countUserMovies($data->user_id);
                    } else {
                        $movies = $movie->getAll();
                        $numOfMovies = $movie->countMovies();
                    }

                    $arr['count'] = $numOfMovies;
                    $arr['data'] = $movie->makeMovieCard($movies);
                    $arr['data_type'] = "vote_for_movie";
                    echo json_encode($arr);
                }




                //----------------------------------------------------------------------------------------------------------------------------- 
            } else if ($data->data_type == 'search_movie') {


                $movieTitle = $data->dataName;
                $movies = $movie->moviesSearch($movieTitle);
                $numOfMovies = $movie->countSearchMovies($movieTitle);
                $arr['data'] = $movie->makeMovieCard($movies);
                $arr['count'] = $numOfMovies;
                $arr['data_type'] = "search_movie";

                echo json_encode($arr);

                //----------------------------------------------------------------------------------------------------------------------------- 

            } else if ($data->data_type == 'filter_movies') {

                $movies = "";

                if ($data->value == "all") {

                    $movies = $movies = $movie->getAll();
                } else 
                if ($data->value == "likes") {

                    $movies = $movie->sortByLikes();
                } else 
                if ($data->value == "hates") {

                    $movies = $movie->sortByHates();
                } else if ($data->value == "date") {

                    $movies = $movie->sortByDate();
                }


                $numOfMovies = $movie->countMovies();
                $arr['data'] = $movie->makeMovieCard($movies);
                $arr['data_type'] = "filter_movies";

                echo json_encode($arr);
            }
        }
    }
}
