<?php
// config.php - Configuración de conexión a la base de datos

// Configuración de la base de datos para Hostinger
// NOTA: Cambiar estos valores por los de tu hosting
define('DB_HOST', 'localhost'); // En Hostinger generalmente es localhost
define('DB_NAME', 'cremeria_raiz'); // Nombre de tu base de datos
define('DB_USER', 'tu_usuario_db'); // Tu usuario de base de datos
define('DB_PASS', 'tu_password_db'); // Tu contraseña de base de datos
define('DB_CHARSET', 'utf8mb4');

// Configuración de sesión
session_start();

// Clase para manejar la conexión a la base de datos
class Database {
    private $connection;
    
    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, inténtalo más tarde.");
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error en la consulta: " . $e->getMessage());
            throw new Exception("Error en la consulta a la base de datos.");
        }
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Función para redirigir
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Función para limpiar datos de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para mostrar mensajes de error/éxito
function showMessage($message, $type = 'error') {
    $_SESSION['message'] = [
        'text' => $message,
        'type' => $type
    ];
}

// Función para obtener y limpiar mensaje
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Instancia global de la base de datos
$db = new Database();
?>