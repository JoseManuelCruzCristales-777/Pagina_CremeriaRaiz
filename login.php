<?php
// login.php - Página de inicio de sesión
require_once 'config.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = cleanInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validaciones básicas
    if (empty($username) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        try {
            // Buscar usuario en la base de datos
            $sql = "SELECT id, username, password_hash, created_at FROM users WHERE username = :username LIMIT 1";
            $user = $db->fetch($sql, ['username' => $username]);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Login exitoso
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_time'] = time();
                
                // Actualizar último acceso
                $updateSql = "UPDATE users SET last_login = NOW() WHERE id = :id";
                $db->query($updateSql, ['id' => $user['id']]);
                
                showMessage('Bienvenido, ' . $user['username'] . '!', 'success');
                redirect('dashboard.php');
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = 'Error interno del servidor. Por favor, inténtalo más tarde.';
        }
    }
}

// Obtener mensaje de sesión si existe
$message = getMessage();
if ($message) {
    if ($message['type'] == 'success') {
        $success = $message['text'];
    } else {
        $error = $message['text'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Cremería Raíz</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Lora:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #1E3A8A, #F26F21); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="login-form">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="font-family: 'Montserrat', sans-serif; color: #1E3A8A; margin-bottom: 10px;">Panel de Administración</h1>
            <p style="color: #666; font-size: 0.9rem;">Cremería Raíz</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Usuario
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autocomplete="username"
                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
                    placeholder="Ingresa tu nombre de usuario"
                >
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Contraseña
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="Ingresa tu contraseña"
                >
            </div>
            
            <button type="submit" class="form-submit">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.html" style="color: #666; text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Volver al sitio web
            </a>
        </div>
        
        <!-- Credenciales de prueba (remover en producción) -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #F26F21;">
            <h4 style="margin: 0 0 10px 0; color: #1E3A8A; font-size: 0.9rem;">Credenciales de prueba:</h4>
            <p style="margin: 5px 0; font-size: 0.8rem; color: #666;">
                <strong>Usuario:</strong> admin<br>
                <strong>Contraseña:</strong> admin123
            </p>
            <p style="margin: 5px 0; font-size: 0.75rem; color: #999; font-style: italic;">
                ⚠️ Cambiar estas credenciales en producción
            </p>
        </div>
    </div>
    
    <script>
        // Validación del formulario en frontend
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Por favor, completa todos los campos.');
                return false;
            }
            
            if (username.length < 3) {
                e.preventDefault();
                alert('El nombre de usuario debe tener al menos 3 caracteres.');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres.');
                return false;
            }
            
            return true;
        });
        
        // Enfocar en el primer campo
        document.getElementById('username').focus();
        
        // Manejar Enter para enviar formulario
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html>