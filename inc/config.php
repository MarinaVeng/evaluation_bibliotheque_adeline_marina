<?php



$dbHost = 'localhost';
$dbName = 'bibliotheque_evaluation';
$dbUser = 'root';
$dbPassword = 'root';



try {
    $db = new PDO(
        "mysql:host=$dbHost;dbname=$dbName",
        $dbUser,
        $dbPassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
} catch (PDOException $e) {
    die('Erreur lors de la connexion : ' . $e->getMessage());
}
