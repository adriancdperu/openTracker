<?php

/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Wuild
 * @package openTracker
 */

if(!defined("INCLUDED"))
    die("Access denied");

$this->setTitle("Admin - Create account");

try {

    if (isset($_POST['register'])) {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['password2']))
            throw new Exception("Missing information");

        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $email = $_POST['email'];

        if (strlen($username) < 4)
            throw new Exception("Username is to short, minimum 4 characters");

        if (strlen($password) < 6)
            throw new Exception("Password is to short, minimum 6 characters");

        if (!validate_string($username))
            throw new Exception("Invalid characters in the username");

        if ($password != $password2)
            throw new Exception("Passwords did not match");

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exception("Invalid email address");

        $db = new DB("users");
        $db->select("user_email = '" . $email . "'");
        if ($db->numRows())
            throw new Exception("Email is already registered");

        $db = new DB("users");
        $db->select("user_name = '" . $email . "'");
        if ($db->numRows())
            throw new Exception("Username does already exist");

        $id = uniqid(true);
        $passkey = md5(uniqid(true));
        $password_secret = generatePassword(12);
        $password_hash = md5($password_secret . $password . $password_secret);


        $db = new DB("users");
        $db->setColPrefix("user_");
        $db->id = $id;
        $db->name = $username;
        $db->email = $email;
        $db->password = $password_hash;
        $db->password_secret = $password_secret;
        $db->status = 1;
        $db->ip = $_SERVER['REMOTE_ADDR'];
        $db->passkey = $passkey;
        $db->group = 1;
        $db->added = time();
        $db->last_access = time();
        $db->insert();

        $email_body = "
            Click the link below to activate your account.<br />
            <a href='" . page("user", "confirm", "", "", "", "key=" . $password_secret) . "'>" . page("user", "confirm", "", "", "", "key=" . $password_secret) . "</a>
        ";

        sendEmail($email, "Account Confirmation", $email_body);

        echo notice(_t("an confirmation email has been sent to") . " " . $email, "Success!");
    }
    ?>

    <h4><?php echo _t("Create account"); ?></h4>
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
            <tr>
                <td><?php echo _t("Username"); ?></td>
            </tr>
            <tr>
                <td><input type="text" name="username" size="30" /></td>
            </tr>
            <tr>
                <td><?php echo _t("Email"); ?></td>
            </tr>
            <tr>
                <td><input type="text" name="email" size="30" /></td>
            </tr>
            <tr>
                <td><?php echo _t("Password"); ?></td>
            </tr>
            <tr>
                <td><input type="password" name="password" size="30" /></td>
            </tr>
            <tr>
                <td><?php echo _t("Confirm password"); ?></td>
            </tr>
            <tr>
                <td><input type="password" name="password2" size="30" /></td>
            </tr>
            <tr>
                <td><input type="submit" name="register" value="<?php echo _t("Register account"); ?>"></td>
            </tr>
        </table>
    </form>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
