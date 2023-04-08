<?php
try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=login_23', 'root', ''); // connect to database
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // set error m.
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}