<?php

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_email']);
}

function redirectToLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

?>