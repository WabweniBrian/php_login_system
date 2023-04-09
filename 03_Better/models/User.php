<?php

namespace app\models;

use app\Database;

class User
{
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;
    public ?string $hashed_password = null;
    public ?string $remember_me = null;
    public ?array $errors = null;

    public function processData($user)
    {
        $this->username = $user['username'] ?? null;
        $this->email = $user['email'];
        $this->password = $user['password'];
        $this->password_confirmation = $user['password_confirmation'] ?? null;
        $this->remember_me = $user['remember_me'] ?? false;
    }

    public function validateUser(string $type)
    {
        if ($type === 'register') {
            $this->errors = ['username' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''];

            // Validate username
            if (empty($this->username)) {
                $this->errors['username'] = 'Username is required';
            }
            // Validate email
            $existingUser = Database::$db->existingUser($this);
            if (empty($this->email)) {
                $this->errors['email'] = 'Email address is required';
            } else if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = 'Invalid email address';
            } else if ($existingUser->rowCount() > 0) {
                $this->errors['email'] = 'Email address already exists';
            }
            // Validate password
            if (empty($this->password)) {
                $this->errors['password'] = 'Password is required';
            } else if (strlen($this->password) < 4) {
                $this->errors['password'] = 'Password must be at least 4 characters';
            }

            // Validate password confirmation
            if (empty($this->password_confirmation)) {
                $this->errors['password_confirmation'] = 'Confirmation Password is required';
            } else if ($this->password_confirmation !== $this->password) {
                $this->errors['password_confirmation'] = 'Passwords does not match';
            }

            // Store hashed password
            $this->hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        } else {
            $this->errors = ['email' => '', 'password' => '', 'credential_err' => ''];
            // Validate email
            if (empty($this->email)) {
                $this->errors['email'] = 'Email address is required';
            }

            // Validate password
            if (empty($this->password)) {
                $this->errors['password'] = 'Password is required';
            }
        }
        return $this->errors;
    }

    public function setUserCookie($user)
    {
        // Set remember me cookie if checked
        if (isset($user['remember_me']) && $user['remember_me'] == 'on') {
            // Set a cookie to remember the user
            setcookie('remember_me', 'on', time() + 3600 * 24 * 30);
            setcookie('email', $this->email, time() + 3600 * 24 * 30);
            $this->remember_me = true;
        } else {
            // If the checkbox is not checked, delete the cookie
            setcookie('remember_me', '', time() - 3600);
            setcookie('email', '', time() - 3600);
            $this->remember_me = false;
        }
    }

    public function registerUser()
    {
        Database::$db->registerUser($this);
    }

    public function loginUser()
    {
        $this->validateUser('login');
        if (empty(array_filter($this->errors))) {
            $user = Database::$db->loginUser($this);
            if ($user && password_verify($this->password, $user['password'])) {
                $this->setSession();
                header("Location: /");
                exit();
            } else {
                $this->errors['credential_err'] = 'Invalid email or password.';
            }
        } else {
        }
    }

    public function setSession()
    {
        session_start();
        $_SESSION['username'] = $this->username;
        $_SESSION['email'] = $this->email;
    }

    public function logoutUser()
    {
        session_start();
        session_destroy();
        header("Location: /");
    }
}