<?php
    /**
     * Example of a simple controller
     * It will call the model to get the data
     * and then decide which view to display (login form or welcome page)
     * 
     * @author: p.reuter
     * @date: Dec. 2023
     */

    
    // do all necessary includes first
    // __DIR__ allows you to use relative paths explicitly
    require_once(__DIR__."/scripts/php/models/UserModel.php");

    $userModel = new UserModel();

    // p.ex. Homer, Simpson, donut, 123

    // test function check_login(string $login, string $password);
    // test for existing login password
    $login = "donut";
    $password = "123";
    
    $result = $userModel->check_login($login, $password);
    print_r($result);

    // test function check_login(string $login, string $password);
    // test for existing login and wrong password
    $login = "donut";
    $password = "1ded23";
    
    $result = $userModel->check_login($login, $password);
    print_r($result);

    // test function check_login(string $login, string $password);
    // test for non-existing login
    $login = "donutt";
    $password = "1ded23";
    
    $result = $userModel->check_login($login, $password);
    print_r($result);
