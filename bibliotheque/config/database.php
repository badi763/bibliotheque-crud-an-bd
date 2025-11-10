<?php
//désactiver l'affichage des erreurs 
ini_set("display_errors", 1);
error_reporting(E_ALL);

//Configuration de la base de données 
define("DB_HOST", "localhost");
define("DB_NAME", "bibliotheque_crud");
define("DB_USER", "root");
define("DB_PASS", "root");
define("DB_CHARSET", "utf8mb4");

function getDbConnection()
{
    //Data Source Name 
    $dns = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
      //options de la connexion
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    try {
        $pdo = new PDO($dns, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}