<?php



class User
{

    private $error = "";


    public function signup($POST)
    { //not the superglobal $_POST

        $data = array();
        $db = Database::getInstance(); //instatiate database

        $data['username'] = trim($POST['username']);
        $data['email'] = trim($POST['user-email']);
        $data['password'] = trim($POST['user-password']);


        if (empty($data['email'])) {
            $this->error .= "Please enter a valid email<br>";
        }

        if (empty($data['username'])) {
            $this->error .= "Please enter a username<br>";
        }

        if (strlen($data['password'] < 8)) {

            $this->error .= "Please enter a password with at least 8 characters<br>";
        }

        //check if there is alredy a user in database using this email
        $checkQuery = "select * from users where user_email= :email limit 1;";
        $arr['email'] = $data['email'];
        $check = $db->read($checkQuery, $arr);

        if (is_array($check)) { // if it is array it means another user has this email also

            $this->error .= "This email already in use.<br>";
        }
        $arr = false; //reset it 

        if ($this->error == "") {

            $data['password'] = hash('sha1', $data['password']); // hash paswword
            $query = "insert into users(username,user_email,user_password) values (:username,:email,:password);";
            $result = $db->write($query, $data);

            if ($result) {
                header("Location:" . ROOT . "login");
                die;
            } else {
                echo "something went wrong";
            }
        }

        //we will create a session about error or success messages to show them on user
        $_SESSION['error'] = $this->error;
    }


    /*********************************************************************************************************************************************************************/
    /*********************************************************************************************************************************************************************/


    public function login($POST)
    {
        $data = array();
        $db = Database::getInstance();

        //retrieve needed data from post form
        $data['email'] = trim($POST['email']);
        $data['password'] = trim($POST['password']);

        if (empty($data['email'])) {
            $this->error .= "Please enter a valid email<br>";
            echo $this->error;
        }

        if (empty($data['password'])) {
            $this->error .= "Please enter  your password<br>";
            echo $this->error;
        }

        if ($this->error == "") {

            $data['password'] = hash('sha1', $data['password']);

            //check if there is alredy a user in database using this email and password
            $checkQuery = "select * from users where user_email= :email && user_password = :password limit 1;";
            $result = $db->read($checkQuery, $data);

            if (is_array($result)) { // if it is array it means that the user exists
                $_SESSION['user'] =  $result[0]->user_id;
                header("Location:" . ROOT . "home");
                die;
            }

            //if credentials are wrong
            $this->error .= "Your Credentials are wrong<br>";
        }
        $_SESSION['error'] = $this->error;
    }


    /*********************************************************************************************************************************************************************/
    /*********************************************************************************************************************************************************************/


    function check_login($redirect = false, $allowed = array())
    {
        $db = Database::getInstance();

        if (count($allowed) > 0) { // if $allowed param is provided

            $arr['id'] = $_SESSION['user'];
            $query = "select * from users where user_id = :id limit 1"; //get the user 
            $result = $db->read($query, $arr);

            if (is_array($result)) { //if the user indeed exists
                $result = $result[0];
                if (in_array($result->role, $allowed)) { //and if the users role exists in the allowed param 
                    return $result;
                }
            }/*(if not)
             ->in parenthesis bcz if the above will run we will never reach the below redirect. we will reach it only if the above is not true 
              (so this works like an else stmt) */
            header("Location:" . ROOT . "pagenotfound");
            die;
        } else // if the $allowed papram is not provided
        {

            if (isset($_SESSION['user'])) //if there is a session 
            {
                $arr['id'] = $_SESSION['user'];
                $query = "select * from users where user_id = :id limit 1";
                $result = $db->read($query, $arr);
                if (is_array($result)) { //if the user exists (returns an array of objects)
                    return $result[0]; //this will return an array of objects (and also a value of true of false depending if the user exists or not.)
                    // Thats why we use [0] (Even though there may only be 1 result in the array)
                }
            }

            if ($redirect) {
                header("Location:" . ROOT . "login");
                die;
            }
        }
    }




    /*********************************************************************************************************************************************************************/
    /*********************************************************************************************************************************************************************/



    public function logOut()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        header("Location:" . ROOT . "home"); //change it to login if we dont want access to site.
        die;
    }



    /*********************************************************************************************************************************************************************/
    /*********************************************************************************************************************************************************************/

    public function getUser($id)
    {
        $id = (int)$id;
        $db = Database::newInstance(); //instatiate database
        return $db->read("select * from users where user_id = '$id' limit 1");
    }
}
