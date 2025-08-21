<?php
// logout.php - Cerrar sesión
require_once 'config.php';

// Verificar si hay una sesión activa
if (isLoggedIn()) {
    $username = $_SESSION['username'] ?? 'Usuario';
    
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruir la sesión
    session_destroy();
    
    // Iniciar nueva sesión para mostrar mensaje
    session_start();
    showMessage("Sesión cerrada exitosamente. ¡Hasta pronto, {$username}!", 'success');
} else {
    showMessage('No había una sesión activa.', 'info');
}

// Redirigir al login
redirect('login.php');
?>