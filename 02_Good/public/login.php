<?php
session_start();

require_once __DIR__ . './../db.php';

$errors = ['email' => '', 'password' => '', 'credential_err' => ''];
$remember_me_checked = false;
$password = '';

if (isset($_COOKIE['remember_me']) && $_COOKIE['remember_me'] == 'on') {
    $email = $_COOKIE['email'];
    $remember_me_checked = true;
} else {
    $email = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs to prevent SQL injection attacks
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email address is required';
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    // Set remember me cookie if checked
    if (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on') {
        // Set a cookie to remember the user
        setcookie('remember_me', 'on', time() + 3600 * 24 * 30);
        setcookie('email', $email, time() + 3600 * 24 * 30);
        $remember_me_checked = true;
    } else {
        // If the checkbox is not checked, delete the cookie
        setcookie('remember_me', '', time() - 3600);
        setcookie('email', '', time() - 3600);
        $remember_me_checked = false;
    }

    if (!array_filter($errors)) {
        $statement = $pdo->prepare("SELECT * FROM users WHERE email= ?");
        $statement->execute([$email]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Store data in session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $email;

            // Redirect to welcome page
            header("Location: index.php");
        } else {
            // Display an error message if username or password is invalid
            $errors['credential_err'] = 'Invalid username or password.';
        }
    }
}


ob_start();
include_once __DIR__ . './../views/users/login.php';
$output = ob_get_clean();
include_once __DIR__ . './../views/layout/main.php';