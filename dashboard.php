<?php
// dashboard.php - Panel de administración
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isLoggedIn()) {
    showMessage('Debes iniciar sesión para acceder al panel de administración.', 'error');
    redirect('login.php');
}

$error = '';
$success = '';

// Procesar acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'add_product':
                $nombre = cleanInput($_POST['nombre'] ?? '');
                $descripcion = cleanInput($_POST['descripcion'] ?? '');
                $precio = floatval($_POST['precio'] ?? 0);
                $imagen_url = cleanInput($_POST['imagen_url'] ?? '');
                
                if (empty($nombre) || empty($descripcion) || $precio <= 0) {
                    $error = 'Todos los campos son obligatorios y el precio debe ser mayor a 0.';
                } else {
                    $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen_url, created_at) VALUES (:nombre, :descripcion, :precio, :imagen_url, NOW())";
                    $db->query($sql, [
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'imagen_url' => $imagen_url
                    ]);
                    $success = 'Producto agregado exitosamente.';
                }
                break;
                
            case 'edit_product':
                $id = intval($_POST['id'] ?? 0);
                $nombre = cleanInput($_POST['nombre'] ?? '');
                $descripcion = cleanInput($_POST['descripcion'] ?? '');
                $precio = floatval($_POST['precio'] ?? 0);
                $imagen_url = cleanInput($_POST['imagen_url'] ?? '');
                
                if ($id <= 0 || empty($nombre) || empty($descripcion) || $precio <= 0) {
                    $error = 'Datos inválidos para editar el producto.';
                } else {
                    $sql = "UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio, imagen_url = :imagen_url, updated_at = NOW() WHERE id = :id";
                    $result = $db->query($sql, [
                        'id' => $id,
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'imagen_url' => $imagen_url
                    ]);
                    
                    if ($result->rowCount() > 0) {
                        $success = 'Producto actualizado exitosamente.';
                    } else {
                        $error = 'No se pudo actualizar el producto. Verifica que existe.';
                    }
                }
                break;
                
            case 'delete_product':
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    $error = 'ID de producto inválido.';
                } else {
                    $sql = "DELETE FROM productos WHERE id = :id";
                    $result = $db->query($sql, ['id' => $id]);
                    
                    if ($result->rowCount() > 0) {
                        $success = 'Producto eliminado exitosamente.';
                    } else {
                        $error = 'No se pudo eliminar el producto. Verifica que existe.';
                    }
                }
                break;
        }
    } catch (Exception $e) {
        error_log("Error en dashboard: " . $e->getMessage());
        $error = 'Error interno del servidor.';
    }
}

// Obtener todos los productos
try {
    $productos = $db->fetchAll("SELECT * FROM productos ORDER BY created_at DESC");
} catch (Exception $e) {
    $productos = [];
    $error = 'Error al cargar los productos.';
}

// Obtener estadísticas
try {
    $stats = [
        'total_productos' => $db->fetch("SELECT COUNT(*) as count FROM productos")['count'] ?? 0,
        'usuarios_registrados' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'] ?? 0,
        'ultimo_producto' => $db->fetch("SELECT nombre FROM productos ORDER BY created_at DESC LIMIT 1")['nombre'] ?? 'N/A'
    ];
} catch (Exception $e) {
    $stats = [
        'total_productos' => 0,
        'usuarios_registrados' => 0,
        'ultimo_producto' => 'N/A'
    ];
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
    <title>Panel de Administración - Cremería Raíz</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Lora:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #FDFBF6;
            padding-top: 0;
        }
        .dashboard-nav {
            background-color: #1E3A8A;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .dashboard-nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-brand {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
        }
        .nav-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .user-info {
            font-size: 0.9rem;
        }
        .btn-outline {
            border: 1px solid white;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            background-color: white;
            color: #1E3A8A;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }
        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        .close:hover {
            color: #333;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .dashboard-nav .container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="dashboard-nav">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-cheese"></i> Cremería Raíz - Panel de Administración
            </div>
            <div class="nav-actions">
                <span class="user-info">
                    <i class="fas fa-user"></i> Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="index.html" class="btn-outline" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Ver Sitio
                </a>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>
    
    <div class="dashboard">
        <div class="container">
            <!-- Header del Dashboard -->
            <div class="dashboard-header">
                <h1 class="dashboard-title">
                    <i class="fas fa-tachometer-alt"></i> Panel de Control
                </h1>
                <button class="cta-button" onclick="openModal('addProductModal')">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>
            
            <!-- Mensajes -->
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
            
            <!-- Estadísticas -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_productos']; ?></div>
                    <div class="stat-label">Productos Registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['usuarios_registrados']; ?></div>
                    <div class="stat-label">Usuarios del Sistema</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        <?php echo strlen($stats['ultimo_producto']) > 15 ? substr($stats['ultimo_producto'], 0, 15) . '...' : $stats['ultimo_producto']; ?>
                    </div>
                    <div class="stat-label">Último Producto</div>
                </div>
            </div>
            
            <!-- Tabla de Productos -->
            <div class="products-table">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-tag"></i> Nombre</th>
                            <th><i class="fas fa-align-left"></i> Descripción</th>
                            <th><i class="fas fa-dollar-sign"></i> Precio</th>
                            <th><i class="fas fa-image"></i> Imagen</th>
                            <th><i class="fas fa-calendar"></i> Fecha</th>
                            <th><i class="fas fa-cogs"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                                    <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                    No hay productos registrados aún.<br>
                                    <small>Haz clic en "Agregar Producto" para comenzar.</small>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['id']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td>
                                        <?php 
                                        $desc = htmlspecialchars($producto['descripcion']);
                                        echo strlen($desc) > 50 ? substr($desc, 0, 50) . '...' : $desc;
                                        ?>
                                    </td>
                                    <td>$<?php echo number_format($producto['precio'], 2); ?> MXN</td>
                                    <td>
                                        <?php if (!empty($producto['imagen_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <i class="fas fa-image" style="color: #ccc;"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($producto['created_at'])); ?></td>
                                    <td>
                                        <button class="edit-btn" onclick="editProduct(<?php echo htmlspecialchars(json_encode($producto)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-btn" onclick="deleteProduct(<?php echo $producto['id']; ?>, '<?php echo htmlspecialchars($producto['nombre']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Agregar Producto -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addProductModal')">&times;</span>
            <h2 style="margin-bottom: 20px; color: #1E3A8A; font-family: 'Montserrat', sans-serif;">
                <i class="fas fa-plus-circle"></i> Agregar Nuevo Producto
            </h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_product">
                <div class="form-group">
                    <label for="add_nombre">Nombre del Producto</label>
                    <input type="text" id="add_nombre" name="nombre" required placeholder="Ej: Queso Oaxaca Tradicional">
                </div>
                <div class="form-group">
                    <label for="add_descripcion">Descripción</label>
                    <textarea id="add_descripcion" name="descripcion" required rows="3" 
                              placeholder="Describe las características del producto..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="add_precio">Precio (MXN)</label>
                        <input type="number" id="add_precio" name="precio" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="add_imagen">URL de Imagen</label>
                        <input type="url" id="add_imagen" name="imagen_url" placeholder="https://ejemplo.com/imagen.jpg">
                    </div>
                </div>
                <button type="submit" class="form-submit">
                    <i class="fas fa-save"></i> Guardar Producto
                </button>
            </form>
        </div>
    </div>
    
    <!-- Modal Editar Producto -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editProductModal')">&times;</span>
            <h2 style="margin-bottom: 20px; color: #1E3A8A; font-family: 'Montserrat', sans-serif;">
                <i class="fas fa-edit"></i> Editar Producto
            </h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit_product">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_nombre">Nombre del Producto</label>
                    <input type="text" id="edit_nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="edit_descripcion">Descripción</label>
                    <textarea id="edit_descripcion" name="descripcion" required rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_precio">Precio (MXN)</label>
                        <input type="number" id="edit_precio" name="precio" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_imagen">URL de Imagen</label>
                        <input type="url" id="edit_imagen" name="imagen_url">
                    </div>
                </div>
                <button type="submit" class="form-submit">
                    <i class="fas fa-save"></i> Actualizar Producto
                </button>
            </form>
        </div>
    </div>
    
    <!-- Formulario oculto para eliminar -->
    <form id="deleteForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" value="delete_product">
        <input type="hidden" id="delete_id" name="id">
    </form>
    
    <script>
        // Funciones para manejar modales
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }
        
        // Función para editar producto
        function editProduct(producto) {
            document.getElementById('edit_id').value = producto.id;
            document.getElementById('edit_nombre').value = producto.nombre;
            document.getElementById('edit_descripcion').value = producto.descripcion;
            document.getElementById('edit_precio').value = producto.precio;
            document.getElementById('edit_imagen').value = producto.imagen_url || '';
            openModal('editProductModal');
        }
        
        // Función para eliminar producto
        function deleteProduct(id, nombre) {
            if (confirm(`¿ Estás seguro de que deseas eliminar el producto "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Cerrar modales con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.style.display = 'none';
                });
            }
        });
        
        // Auto-hide mensajes después de 5 segundos
        setTimeout(function() {
            const messages = document.querySelectorAll('.error-message, .success-message');
            messages.forEach(message => {
                message.style.transition = 'opacity 0.5s ease';
                message.style.opacity = '0';
                setTimeout(() => {
                    if (message.parentNode) {
                        message.remove();
                    }
                }, 500);
            });
        }, 5000);
        
        console.log('Dashboard cargado exitosamente');
    </script>
</body>
</html>