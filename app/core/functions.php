<?php


function check_message()
{
    if (isset($_SESSION['error']) && $_SESSION['error'] != "") {

        echo $_SESSION['error'];
        unset($_SESSION['error']); // we unset so we dont have to see it every time the page refreshes for example
    }
}


function show($data)
{
    echo "<pre>";
    print_r($data);
    echo "<pre>";
}
