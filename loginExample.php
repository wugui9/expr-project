<!-- 
    Example HTML file to showcase a simple login form which uses
        - a php controller script (logic-related aspects) that calls a php model script (data-related aspects)
        - a php view script (UI-related aspects)

* @author: w.delamare
* @date: Dec. 2023
 -->

<?php
    // do all necessary includes first
    // __DIR__ allows you to use relative paths explicitly
    require_once(__DIR__."/scripts/php/views/includes.php");

    // check if the form has been submitted and has been returned with an error
    if (isset($_GET['error'])) {
        // Let the controller perform all the checks (logic checks, e.g., empty, valid login/password pair)
        if (strcmp($_GET['error'], "missing") == 0) {
            $something_to_say = "Missing login and/or password";
        }
        elseif (strcmp($_GET['error'], "invalid") == 0) {
            $something_to_say = "Invalid login and/or password.";
        }
        elseif (strcmp($_GET['error'], "notallowed") == 0) {
            $something_to_say = "You should login first.";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/example.css">
        <title>Login Example</title>
    </head>
    <body>
        
        <?php include_header(); ?>

        <?php 
            // if an error happened
            if (isset($something_to_say)) {
                include_error_message($something_to_say);
            }
        ?>

        <form method="post" action="./scripts/php/controllers/loginController.php">
            <fieldset>
                <legend>Login</legend>
                <input type="text" placeholder="login" id="login" name="login">
                <input type="password" placeholder='password' id='pwd' name="pwd">
                <button type="submit">Submit</button>
            </fieldset>
        </form>

        <?php include_footer(); ?>

    </body>
</html>