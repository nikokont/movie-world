<?php

class Database
{
    /* 
     * since we will instatiate this function multiple times, multiple copies of the class, so multiple connections 
     * are created and this may cause security issues, or run memory down. We need to combat that so we  have only 
     * one connection to the database. So we will use static to create only one connection (or instance).
     * Keep in mnd that there will be times where we dont need only one instance bcz this may cause errors for functions to have the same instance,
     * we must create a new instance but only when needed. 
     * (We create a new one when we get an error unknown method in a database function (read,write)). The function that will help with this is : newInstance()
     * which will just create a new instance
     */

    public static $conn;

    public function __construct()
    { //will run as soon as the Database class is instatiated.

        try {
            //these are defined in the config.php file
            $string = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            self::$conn = new PDO($string, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance()
    { //will run as soon as the Database class is instatiated.

        if (self::$conn) { // if the connection is set 
            return self::$conn; //get the same instance
        }
        //if connection is not set
        return $instance = new self(); // (same as saying new Database)
    }

    public static function newInstance()
    { 

        return $instance = new self(); 
    }

    /*
     * we must require an instance of the class in order for these functions to work because they are not static. Again because we only need
     * as less connections as possible to the database thats why we used static function to create a connection (in constructor) because in other case when we instatiated the class
     * to create a connection, multiple connection objects would be created, so we would have multiple connections. But here in the same connection we
     * will need many times to read from/write to database, thats why they are not static.
    */

    //the user will supply 2 things:the query and sometimes some data

    //function to read from database
    public function read($query, $data = array())
    {
        $stmt = self::$conn->prepare($query);
        $result = $stmt->execute($data);

        if ($result) {
            $data = $stmt->fetchAll(PDO::FETCH_OBJ); //fetch results in form of object
            if (is_array($data) && count($data) > 0) // if we have an array it means that we have indeed data
            {
                return $data;
            }
            return false;
        }
    }


    //function to write to database
    public function write($query, $data = array())
    {
        $stmt = self::$conn->prepare($query);
        $result = $stmt->execute($data);

        if ($result) {
            return true;
        }
        return false;
    }
}
