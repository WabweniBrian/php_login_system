<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
}

ob_start();
include_once __DIR__ . './../views/users/home.php';
$output = ob_get_clean();
include_once __DIR__ . './../views/layout/main.php';