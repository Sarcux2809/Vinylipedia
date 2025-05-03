<?php
session_start();
ini_set('display_errors', 1);  // Muestra los errores
error_reporting(E_ALL);        // Muestra todos los errores

header('Content-Type: application/json');

// Config de conexión
$host = 'localhost';
$db   = 'vinylpedia';
$user = 'root';
$pass = 'M&aS2XsP';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
  } catch (PDOException $e) {
    // Mostrar detalles del error PDO
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
  }
  

// Leer JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? null;

switch ($action) {
  case 'login':
    login($pdo, $input);
    break;
  case 'register':
    register($pdo, $input);
    break;
  case 'recover':
    recover($pdo, $input);
    break;
  default:
    echo json_encode(['success' => false, 'error' => 'Acción no definida']);
    break;
}

// FUNCIONES
function login($pdo, $input) {
  $email = $input['email'] ?? '';
  $password = $input['password'] ?? '';

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
      'id' => $user['id'],
      'username' => $user['username'],
      'email' => $user['email']
    ];
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'error' => 'Correo o contraseña incorrectos']);
  }
}

function register($pdo, $input) {
  $username = $input['username'] ?? '';
  $email = $input['email'] ?? '';
  $password = $input['password'] ?? '';

  // Validar duplicado
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Ya existe una cuenta con ese correo']);
    return;
  }

  $hashed = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  $stmt->execute([$username, $email, $hashed]);

  echo json_encode(['success' => true]);
}

function recover($pdo, $input) {
  $email = $input['email'] ?? '';

  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  if ($stmt->fetch()) {
    // Simular envío (en app real se enviaría email)
    echo json_encode(['success' => true, 'message' => 'Instrucciones de recuperación enviadas']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Correo no registrado']);
  }
}
