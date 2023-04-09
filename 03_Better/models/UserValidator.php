<?php

namespace app\models;

class UserValidator
{
    public static function validateRegister(array $user): array
    {
        $errors = ['username' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''];
        // Validate username
        if (empty($user['username'])) {
            $errors['username'] = 'Username is required';
        }

        // Validate email
        if (empty($user['email'])) {
            $errors['email'] = 'Email address is required';
        } else if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address';
        }

        // Validate password
        if (empty($user['password'])) {
            $errors['password'] = 'Password is required';
        } else if (strlen($user['password']) < 4) {
            $errors['password'] = 'Password must be at least 4 characters';
        }

        // Validate password confirmation
        if (empty($user['password_confirmation'])) {
            $errors['password_confirmation'] = 'Confirmation Password is required';
        } else if ($user['password_confirmation'] !== $user['password']) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        return $errors;
    }

    public static function validateLogin(array $user): array
    {
        $errors = ['email' => '', 'password' => '', 'credential_err' => ''];

        // Validate email
        if (empty($user['email'])) {
            $errors['email'] = 'Email address is required';
        }

        // Validate password
        if (empty($user['password'])) {
            $errors['password'] = 'Password is required';
        }

        return $errors;
    }
}