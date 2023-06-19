<?php

class Movie
{


    public function create($data, $files)
    {
        $_SESSION['error'] = "";

        $db = Database::newInstance();

        $date = date("Y-m-d h:i:s");
        $arr['title'] = ucwords($data->dataTitle); //ucwords for first letter to be capital
        $arr['description'] = ucwords($data->dataDescr);
        $arr['uploaded_date'] = $date;
        $arr['uploaded_by'] = $data->dataUploadedBy;
        $arr['cover_photo'] = "";

        //allowed file types
        $allowed[] = "image/jpeg";

        //allowed size
        $size = 10; //-> to megabytes
        $size = ($size * 1024 * 1024);

        //directory to upload images
        $folder = "uploads/";

        //create directory if it does not exist
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        //check for files

        if (empty($files['dataImage']['name'])) {

            $_SESSION['error'] .= "No Image is passed here";
        } else {

            if ($files['dataImage']['size'] > $size) { //check if the file is in accepted size

                $_SESSION['error'] .= "please enter a image up to 10 MB";
            } else if (!in_array($files['dataImage']['type'], $allowed)) { //if file format is not supported

                $_SESSION['error'] .= "The file format is not supported";
            } else {
                //capture the file destination. We create a folder uploads in public folder bcz in any other folder will not be accesible and will not be displayed
                $destination = $folder . $files['dataImage']['name'];
                move_uploaded_file($files['dataImage']['tmp_name'], $destination);
                $arr['cover_photo'] = $destination;
            }
        }

        if (!isset($_SESSION['error']) || $_SESSION['error'] == "") {

            $query = "insert into movies (movie_title, movie_description, movie_uploaded_date , movie_uploaded_by ,movie_photo) values (:title,:description,:uploaded_date,:uploaded_by,:cover_photo)";
            $check = $db->write($query, $arr);

            if ($check) {
                return true;
            }
        }

        return false; // if something went wrong
    }

    /*-----------------------------------------------------------------------------------------------------------------------------------*/


    public function getAll()
    {


        $db = Database::newInstance();
        $currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;


        $query = "SELECT
        m.movie_id,
        m.movie_title,
        m.movie_description,
        m.movie_uploaded_date,
        m.movie_uploaded_by,
        m.movie_photo,
        u.username AS uploader_username,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 1 AND uv.user_vote_movie = m.movie_id) AS like_count,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 0 AND uv.user_vote_movie = m.movie_id) AS dislike_count,
        IF(uv.user_vote_like_hate = 1, 'liked', IF(uv.user_vote_like_hate = 0, 'disliked', 'not_voted')) AS user_vote_status
        FROM
            movies m
        LEFT JOIN
            users_vote uv ON m.movie_id = uv.user_vote_movie AND uv.user_vote_user = :currentUser
        LEFT JOIN
            users u ON m.movie_uploaded_by = u.user_id
        GROUP BY
            m.movie_id
        ORDER BY
        m.movie_id DESC";
        $params = array(':currentUser' => $currentUser);
        return $db->read($query, $params);
    }





    /*-----------------------------------------------------------------------------------------------------------------------------------*/

    public function getUserMovies($userId)
    {


        $db = Database::newInstance();
        $currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;


        $query = "SELECT
        m.movie_id,
        m.movie_title,
        m.movie_description,
        m.movie_uploaded_date,
        m.movie_uploaded_by,
        m.movie_photo,
        u.username AS uploader_username,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 1 AND uv.user_vote_movie = m.movie_id) AS like_count,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 0 AND uv.user_vote_movie = m.movie_id) AS dislike_count,
        IF(uv.user_vote_like_hate = 1, 'liked', IF(uv.user_vote_like_hate = 0, 'disliked', 'not_voted')) AS user_vote_status
        FROM
            movies m
        LEFT JOIN
            users_vote uv ON m.movie_id = uv.user_vote_movie AND uv.user_vote_user = :currentUser
        LEFT JOIN
            users u ON m.movie_uploaded_by = u.user_id
        WHERE  m.movie_uploaded_by=:id

        GROUP BY
            m.movie_id
        ORDER BY
        m.movie_id DESC";
        $params = array(':currentUser' => $currentUser, ':id' => $userId);
        return $db->read($query, $params);
    }




    /*-----------------------------------------------------------------------------------------------------------------------------------*/


    public function movieVote($data)
    {

        $_SESSION['error'] = "";

        $db = Database::newInstance();

        $user = $data->voter;
        $movie = $data->movie;
        $vote = $data->vote;


        //check if the user has already voted
        $query = "select * from users_vote where user_vote_user =:user and user_vote_movie=:movie limit 1";
        $params = array('user' => $user, 'movie' => $movie);
        $check = $db->read($query, $params);


        if (!empty($check)) { //if he has voted, update his vote

            $voteId = $check[0]->user_vote_id;


            if ($check[0]->user_vote_like_hate !=  $vote) { //if voted different update vote

                $query = "update users_vote set user_vote_like_hate = :vote where user_vote_id=:id";
                $updateParams = array('vote' => $vote, 'id' => $voteId);
                $check = $db->write($query, $updateParams);
            } else  if ($check[0]->user_vote_like_hate ==  $vote) {  //if voted same retract(delete) vote

                $query = "delete from users_vote where user_vote_id=:id";
                $deleteParams = array('id' => $voteId);
                $check = $db->write($query, $deleteParams);
            }
        } else { //if he has not voted

            $query = "insert into users_vote (user_vote_like_hate, user_vote_user , user_vote_movie) values (:vote,:user,:movie)";
            $insertParams = array('vote' => $vote, 'user' => $user, 'movie' => $movie);
            $check = $db->write($query, $insertParams);


            if ($check) {
                return true;
            } else {
                $_SESSION['error'] .= "There was an error";
                return false;
            }
        }
    }


    /*-----------------------------------------------------------------------------------------------------------------------------------*/

    public function moviesSearch($value) // quick search by name
    {


        $db = Database::newInstance();
        $currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;


        $query = "SELECT
        m.movie_id,
        m.movie_title,
        m.movie_description,
        m.movie_uploaded_date,
        m.movie_uploaded_by,
        m.movie_photo,
        u.username AS uploader_username,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 1 AND uv.user_vote_movie = m.movie_id) AS like_count,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 0 AND uv.user_vote_movie = m.movie_id) AS dislike_count,
        IF(uv.user_vote_like_hate = 1, 'liked', IF(uv.user_vote_like_hate = 0, 'disliked', 'not_voted')) AS user_vote_status
        FROM
            movies m
        LEFT JOIN
            users_vote uv ON m.movie_id = uv.user_vote_movie AND uv.user_vote_user = :currentUser
        LEFT JOIN
            users u ON m.movie_uploaded_by = u.user_id
        WHERE movie_title LIKE '%$value%'
        GROUP BY
            m.movie_id
        ORDER BY
        m.movie_id DESC";
        $params = array(':currentUser' => $currentUser);
        return $db->read($query, $params);
    }


    /*-----------------------------------------------------------------------------------------------------------------------------------*/
    public function countMovies()
    {
        $db = Database::newInstance();

        $moviesNum = $db->read("select movie_id from movies");

        if (is_array($moviesNum)) {
            return  count($moviesNum);
        }
        return  0;
    }

    /*-----------------------------------------------------------------------------------------------------------------------------------*/

    public function countSearchMovies($value)
    {
        $db = Database::newInstance();

        $moviesNum = $db->read("select * from movies where movie_title LIKE '%$value%'");

        if (is_array($moviesNum)) {
            return  count($moviesNum);
        }
        return  0;
    }

    /*-----------------------------------------------------------------------------------------------------------------------------------*/


    public function countUserMovies($userId)
    {
        $db = Database::newInstance();

        $moviesNum = $db->read("select movie_id from movies where movie_uploaded_by ='$userId'");

        if (is_array($moviesNum)) {
            return  count($moviesNum);
        }
        return  0;
    }

    /*-----------------------------------------------------------------------------------------------------------------------------------*/

    public function sortByLikes()
    {
        $db = Database::newInstance();



        $currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;


        $query = "SELECT
        m.movie_id,
        m.movie_title,
        m.movie_description,
        m.movie_uploaded_date,
        m.movie_uploaded_by,
        m.movie_photo,
        u.username AS uploader_username,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 1 AND uv.user_vote_movie = m.movie_id) AS like_count,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 0 AND uv.user_vote_movie = m.movie_id) AS dislike_count,
        IF(uv.user_vote_like_hate = 1, 'liked', IF(uv.user_vote_like_hate = 0, 'disliked', 'not_voted')) AS user_vote_status
    FROM
        movies m
    LEFT JOIN
        users_vote uv ON m.movie_id = uv.user_vote_movie AND uv.user_vote_user = :currentUser
    LEFT JOIN
        users u ON m.movie_uploaded_by = u.user_id
    GROUP BY
        m.movie_id
        ORDER BY
        like_count DESC";
        $params = array(':currentUser' => $currentUser);
        return $db->read($query, $params);
    }

    /*-----------------------------------------------------------------------------------------------------------------------------------*/

    public function sortByHates()
    {
        $db = Database::newInstance();

        $currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;


        $query = "SELECT
        m.movie_id,
        m.movie_title,
        m.movie_description,
        m.movie_uploaded_date,
        m.movie_uploaded_by,
        m.movie_photo,
        u.username AS uploader_username,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 1 AND uv.user_vote_movie = m.movie_id) AS like_count,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 0 AND uv.user_vote_movie = m.movie_id) AS dislike_count,
        IF(uv.user_vote_like_hate = 1, 'liked', IF(uv.user_vote_like_hate = 0, 'disliked', 'not_voted')) AS user_vote_status
        FROM
            movies m
        LEFT JOIN
            users_vote uv ON m.movie_id = uv.user_vote_movie AND uv.user_vote_user = :currentUser
        LEFT JOIN
            users u ON m.movie_uploaded_by = u.user_id
        GROUP BY
        m.movie_id
        ORDER BY
        dislike_count DESC";
        $params = array(':currentUser' => $currentUser);
        return $db->read($query, $params);
    }





    /*-----------------------------------------------------------------------------------------------------------------------------------*/


    public function sortByDate()
    {
        $db = Database::newInstance();
        $currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;
        $query = "SELECT
        m.movie_id,
        m.movie_title,
        m.movie_description,
        m.movie_uploaded_date,
        m.movie_uploaded_by,
        m.movie_photo,
        u.username AS uploader_username,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 1 AND uv.user_vote_movie = m.movie_id) AS like_count,
        (SELECT COUNT(*) FROM users_vote uv WHERE uv.user_vote_like_hate = 0 AND uv.user_vote_movie = m.movie_id) AS dislike_count,
        IF(uv.user_vote_like_hate = 1, 'liked', IF(uv.user_vote_like_hate = 0, 'disliked', 'not_voted')) AS user_vote_status
        FROM
            movies m
        LEFT JOIN
            users_vote uv ON m.movie_id = uv.user_vote_movie AND uv.user_vote_user = :currentUser
        LEFT JOIN
            users u ON m.movie_uploaded_by = u.user_id
        GROUP BY
        m.movie_id
        ORDER BY
        m.movie_uploaded_date ASC";
        $params = array(':currentUser' => $currentUser);
        return $db->read($query, $params);


        return $db->read($query);
    }




    public function makeMovieCard($movies)
    {

        $result = ""; // we put reslut here bcz a function must return something.. so we will tore the whole code below to this result variable and return it
        if (is_array($movies)) {
            foreach ($movies as $movie) {


                $voter = "";

                if (isset($_SESSION['user'])) {
                    $voter .= $_SESSION['user'];
                }


                $voteArgs =  $movie->movie_id . " ,'" .  $voter . "',";

                $result .= '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">';
                $result .= '
                           
                <div class="single-movie">
                <div class="movie-cover-photo">
                    <img src=' . ROOT . $movie->movie_photo . '> 
                    <div class="overlay">
                        <span class="overlay-text">' . $movie->movie_description . '</span>
                    </div>
                </div>

                <div class="movie-info">

                    <div class="movie-title-info">
                        <h5 class="movie-title">' . substr($movie->movie_title, 0, 30) . '...</h5>
                    </div>

                    <div class="movie-likes-votes">
                        <div class="movie-likes-hates">
                            <span class="movie-likes-count"><i class="fa-solid fa-thumbs-up"></i> ' . $movie->like_count . '</span>
                            <span class="movie-dislikes-count"><i class="fa-solid fa-thumbs-down"></i> ' . $movie->dislike_count . '</span>
                        </div>';

                if (isset($_SESSION['user'])) {

                    if ($movie->movie_uploaded_by != $_SESSION['user']) { //if the uploader of the movie is not the same with the logged in user 
                        $result .= '
                                        <div class="movie-votes">';

                        if ($movie->user_vote_status == "liked") {
                            $result .= '
                                                <button onclick="voteForMovie(1, ' . $voteArgs . ')" style="border:0; border-bottom:2px solid green" class="vote-like-button">Liked</button>
                                                <button onclick="voteForMovie(0, ' . $voteArgs . ')" style="border:0; border-bottom:1px solid #707070;  background-color:transparent"  class="vote-hate-button">Hate</button> ';
                        } else if ($movie->user_vote_status == "disliked") {
                            $result .= '
                                                <button onclick="voteForMovie(1, ' . $voteArgs . ')" style="background-color:transparent; border:0; border-bottom:1px solid #707070;"  class="vote-like-button">Like</button>
                                                <button  onclick="voteForMovie(0, ' . $voteArgs . ')" style="border:0; border-bottom:2px solid red"  class="vote-hate-button">Hated</button> ';
                        } else if ($movie->user_vote_status == "not_voted") {
                            $result .= '
                                                <button  onclick="voteForMovie(1, ' . $voteArgs . ')" style="background-color:transparent; border:0; border-bottom:1px solid #707070;"  class="vote-like-button">Like</button>
                                                <button  onclick="voteForMovie(0, ' . $voteArgs . ')" style="background-color:transparent; border:0; border-bottom:1px solid #707070;"  class="vote-hate-button">Hate</button> ';
                        }

                        $result .= '</div>';
                    }
                }

                $result .= '</div>


                    <div class="upload-info">

                        <small>Uploaded by: <b> <a href=' . ROOT . 'user_movies/' . $movie->movie_uploaded_by . '>' . $movie->uploader_username . '</a></b>  | 
                        <small>Uploaded date: <b>'  . date('d-m-Y', strtotime($movie->movie_uploaded_date))  . '</b></small>

                     </div>
                </div>

            </div><!--single-movie-end-->';


                $result .= "</div>";
            }
        } else {
            $result .= "<div style='text-align:center; color:#fff;'><h4>No Movies Found.</h4></div>";
        }
        return $result;
    }
}
