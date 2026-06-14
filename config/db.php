<?php
// Configuration dynamique : prend les variables de Render en ligne, ou tes paramètres si tu es en local
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'lms_db';
$username = getenv('DB_USER') ?: 'blondelle';
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : 'LmsPassword123!';

try {
    // Connexion PDO avec encodage UTF-8 pour la gestion des accents
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configuration des options d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>