<?php
session_start();

// Connect to database
try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=login_23', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$errors = ['email' => '', 'password' => '', 'credential_err' => ''];
$remember_me_checked = false;
$password = '';

if (isset($_COOKIE['remember_me']) && $_COOKIE['remember_me'] == 'on') {
    $email = $_COOKIE['email'];
    $remember_me_checked = true;
} else {
    $email = '';
}

// Process form data when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs to prevent SQL injection attacks
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate user inputs
    if (empty($email)) {
        $errors['email'] = "Email address is required";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
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

    // If there are no errors, attempt to log in the user
    if (!array_filter($errors)) {
        // Check if the username exists in the database
        $statement = $pdo->prepare("SELECT * FROM users_table WHERE email=:email");
        $statement->execute([':email' => $email]);
        $user = $statement->fetch();

        // Check if the password matches the hashed password stored in the database
        if ($user &&  password_verify($password, $user['password'])) {
            // Check if the user's account is verified
            if ($user['is_verified'] == 1) {
                // Log in the user by storing their username in a session variable
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $user['username'];

                // Redirect the user to the homepage
                header('Location: index.php');
                exit();
            } else {
                $errors['credential_err'] =  "Your account has not been verified. Please check your email for a verification link.";
            }
        } else {
            $errors['credential_err'] = "Username or Password is incorect";
        }
    }
}

?>




<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <main>
        <div class="container">
            <div class="row justify-content-center align-items-center g-2 min-vh-100">
                <div class="col-sm-12 col-md-6 bg-white rounded p-3">
                    <h3 class="text-center">LOGIN</h3>
                    <form action="" method="post">
                        <div class="mt-4">
                            <h4 class="text-center text-danger"><?= $errors['credential_err'] ?></h4>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Email address.."
                                    value="<?= htmlspecialchars($email)  ?>">
                                <small class="form-text text-muted text-danger"><?= $errors['email'] ?></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password.."
                                    value="<?= htmlspecialchars($password)  ?>">
                                <small class="form-text text-muted text-danger"><?= $errors['password'] ?></small>
                            </div>
                            <div class="input-check mb-3">
                                <input type="checkbox" class="check" placeholder="Placeholder" id="check"
                                    name="remember_me" <?php if ($remember_me_checked) echo 'checked'; ?>>
                                <label for="check">Remember me</label>
                            </div>


                            <div class="d-grid gap-2">
                                <button type="submit" class="btn">Login</button>
                            </div>
                    </form>
                    <div class="mt-3 float-end">
                        <p>Don't have an account yet? <a href="register.php" class="text-primary">Register</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>

</html>