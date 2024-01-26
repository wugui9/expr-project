<?php
    /**
     * This script could have contained a class....
     * For the purpose of showing multiple ways to achieve a goal,
     * this file does not contain a class...
     * 
     * @author: w.delamare
     * @date: Dec. 2023
     */

    require_once(__DIR__."/../models/userModel.php");


    /**
     * The controller will perform all necessary checks (e.g., check if empty, etc.)
     * before in turn calling the model (to get the data)
     * and then deciding which page to display to users
     * 
     */

    // Check if the user comes from the form...
    // Otherwise, trying to cheat the system!
    if (isset($_POST['login']) && isset($_POST['pwd'])) {

        // check if all fields have an input
        if (strlen($_POST['login']) > 0 && strlen($_POST['pwd']) > 0) {
            $userModel = new userModel();
            $result = $userModel->check_login($_POST['login'], $_POST['pwd']);
            // If the search in the db is successful
            if (isset($result['firstname'])) {
                // the controller should redirect to a correct webpage
                // making sure the login is registered throughout the **session**
                // (session_start(), etc...)
                // But this example is long enough
                echo("login ok. Done. Hello " . $result['firstname']);
            }
            else {
                // return to the previous page with an error message 'invalid'
                header("Location: ../../../loginExample.php?error=invalid");    
            }
        }
        else {
            header("Location: ../../../loginExample.php?error=missing");
        }
    }
    else {
        // return to the previous page with an error message 'not allowed without login form!'
        header("Location: ../../../loginExample.php?error=notallowed");
    }


?>