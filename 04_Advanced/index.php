<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <main>
        <header class=" bg-white border-bottom">
            <nav class="container d-flex justify-content-between align-items-center">
                <a href="index.php nav-brand">
                    <h1>Home</h1>
                </a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <div class="mt-5">
            <div class="container py-5 bg-white rounded shadow-sm border-1">
                <h1 class="text-center">Welcome, <?= $_SESSION['username'] ?> </h1>
                <h5 class="text-center">Your email addess: <?= $_SESSION['email'] ?> </h5>
            </div>
        </div>
    </main>
</body>

</html>