<?php
session_start();

require_once __DIR__ . './../db.php';

$username = $email = $password = $password_confirmation = '';
$errors = ['username' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs to prevent SQL injection attacks
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password_confirmation = filter_var(trim($_POST['password_confirmation']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate username
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }


    // Validate email
    $user = $pdo->query("SELECT * FROM users WHERE email = '$email'");
    if (empty($email)) {
        $errors['email'] = 'Email address is required';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address';
    } else if ($user->rowCount() > 0) {
        $errors['email'] = 'Email address already exists';
    }


    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } else if (strlen($password) < 4) {
        $errors['password'] = 'Password must be at least 4 characters';
    }

    // Validate password confirmation
    if (empty($password_confirmation)) {
        $errors['password_confirmation'] = 'Confirmation Password is required';
    } else if ($password_confirmation !== $password) {
        $errors['password_confirmation'] = 'Passwords does not match';
    }

    // Store hashed password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (!array_filter($errors)) {
        $statement = $pdo->prepare("INSERT INTO users (username, email, password) VALUES(?,?,?)");
        $statement->execute([$username, $email, $hashed_password]);

        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        header("Location: index.php");
    }
}



ob_start();
include_once __DIR__ . './../views/users/register.php';
$output = ob_get_clean();
include_once __DIR__ . './../views/layout/main.php';