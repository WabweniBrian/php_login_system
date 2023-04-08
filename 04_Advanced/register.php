<?php
session_start();

// Connect to database
try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=login_23', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Initialize variables
$username = $email = $password = $password_confirmation = '';
$errors = ['username' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''];

// Process form data when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs to prevent SQL injection attacks
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password_confirmation = filter_var($_POST['password_confirmation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate user inputs
    if (empty($username)) {
        $errors['username'] = "Username is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    }

    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }

    if ($password != $password_confirmation) {
        $errors['password_confirmation'] = "Passwords do not match";
    }

    // Check if the username or email is already taken
    $statement = $pdo->prepare("SELECT * FROM users_table WHERE username=:username OR email=:email");
    $statement->execute([':username' => $username, ':email' => $email]);
    $user = $statement->fetch();

    if ($user) {
        if ($user['username'] == $username) {
            $errors['username'] = "Username already taken";
        }

        if ($user['email'] == $email) {
            $errors['username'] = "Email already taken";
        }
    }

    // If there are no errors, register the user
    if (!array_filter($errors)) {
        // Hash the password before storing it in the database
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Generate a verification token
        $verification_token = bin2hex(random_bytes(32));

        // Insert user data into the database
        $statement = $pdo->prepare("INSERT INTO users_table (username, email, password, verification_token) VALUES (:username, :email, :password, :verification_token)");
        $statement->execute([':username' => $username, ':email' => $email, ':password' => $password_hash, ':verification_token' => $verification_token]);

        // Send a verification email to the user
        $to = $email;
        $subject = 'Verify your account';
        $message = "Please click the following link to verify your account:\n\n";
        $message .= "http://localhost/verify.php?token=" . $verification_token;
        $headers = 'From: webmaster@example.com' . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


        if (mail($to, $subject, $message, $headers)) {
            $_SESSION['success'] = "You are now registered and a verification email has been sent to your email address";
        } else {
            $_SESSION['success'] = "There was an error sending a verification email";
        }

        // Store user data in session variables
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;


        // Redirect to home page
        header('location: index.php');
        exit();
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