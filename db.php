<?php
$host = 'localhost';
$db = 'vinylpedia';
$user = 'root';
$pass = 'M&aS2XsP';  // Debes usar la contraseña correcta aquí
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Conexión exitosa a la base de datos"; 
} catch (\PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}
?>
