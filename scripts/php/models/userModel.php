<?php

/**
 * Our model classes (userModel in this example) extends the base class DBModel
 * so that we can factorize every common methods into the super class
 * 
 * Every other model classes (to deal with other data and tables) will follow the same principle
 * 
 * @author: w.delamare
 * @date: Dec. 2023
 */

require_once("DBModel.php");

class userModel extends DBModel {


    /**
     * @return an associative array of all employees with first_name, last_name, id, creation_date (not formatted)
     */
    function check_login(string $login, string $password) {
        $result = [];
        if (!$this->connected) {
            // Something went wrong during the connection to the database.
            // In this example, we simply do not perform the query...
            // A real website should display a message for users to understand while they cannot log in
            return $result;
        }
        // The request uses the MD5() functions since password should not be stored
        // without any protection in the database (i.e., use MD5() to store and retrieve passwords)
        $request = "SELECT * FROM user WHERE login=:login AND password=MD5(:password)";
        $statement = $this->db->prepare($request);
        $statement->execute([
            "login" => $login,
            "password" => $password
        ]);
        $entries = $statement->fetchAll();
        if (count($entries) == 1) {
            $result["firstname"] = $entries[0]['firstname'];
            $result["lastname"] = $entries[0]['lastname'];
        }
        return $result;
    }
}


?>