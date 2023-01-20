<?php

declare (strict_types=1);

function connectDB(): PDO {
    static $db=null;

    if($db===null){
    // Koppla mot databasen
    $dsn='mysql:dbname=tidsrapport;host=localhost';
    $dbUser = 'root';
    $dbPassword = "";
    $db = new PDO($dsn,$dbUser,$dbPassword);
    }
    
    return $db;
}
