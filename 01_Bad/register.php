<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=login_23', 'root', ''); // connect to database
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // set error m.
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


$username = $email = $password = $password_confirmation = '';
$errors = ['username' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs to prevent SQL injection attacks
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password_confirmation = filter_var($_POST['password_confirmation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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



?>




<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
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
                    <h3 class="text-center">REGISTER</h3>
                    <form action="" method="post">
                        <div class="mt-4">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Username.."
                                    value="<?= $username ?>">
                                <small class="form-text text-muted text-danger"><?= $errors['username'] ?></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Email address.."
                                    value="<?= htmlspecialchars($email) ?>">
                                <small class="form-text text-muted text-danger"><?= $errors['email'] ?></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password.."
                                    value="<?= htmlspecialchars($password) ?>">
                                <small class="form-text text-muted text-danger"><?= $errors['password'] ?></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Password Confirmation.."
                                    value="<?= htmlspecialchars($password_confirmation) ?>">
                                <small
                                    class="form-text text-muted text-danger"><?= $errors['password_confirmation'] ?></small>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn">Register</button>
                        </div>
                    </form>
                    <div class="mt-3 float-end">
                        <p>Already have an account? <a href="login.php" class="text-primary">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>

</html>