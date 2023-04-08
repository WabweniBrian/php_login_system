<?php

namespace app;

use PDO;
use PDOException;

class Database
{

    public PDO $pdo;

    public function __construct()
    {
        try {
            # code...
        } catch (PDOException $e) {
            # code...
        }
    }
}