# Cremería Raíz - Sitio Web Completo

## 🧀 Descripción del Proyecto

Sitio web profesional y moderno para "Cremería Raíz" en Oaxaca, que combina elementos visuales inspirados en lala.com.mx y quesosexcelsior.com. Incluye frontend responsivo y sistema de administración backend con PHP + MySQL.

## 🎈 Características Principales

### Frontend
- ✅ Diseño 100% responsivo
- ✅ Header sticky con navegación suave
- ✅ Hero section con imagen full-screen
- ✅ Grid de productos en 3 columnas
- ✅ Sección de historia con layout 2 columnas
- ✅ Footer completo con información de contacto
- ✅ Animaciones CSS y JavaScript
- ✅ Paleta de colores personalizada
- ✅ Tipografía elegante (Montserrat + Lora)

### Backend
- ✅ Sistema de login seguro
- ✅ Panel de administración completo
- ✅ CRUD de productos (Crear, Leer, Actualizar, Eliminar)
- ✅ Base de datos MySQL con tablas optimizadas
- ✅ Validación frontend y backend
- ✅ Protección contra inyección SQL
- ✅ Manejo de sesiones seguro

## 🎨 Paleta de Colores

- **Naranja/Terracota:** `#F26F21` (CTAs y acentos)
- **Azul Profundo:** `#1E3A8A` (fondos secundarios)
- **Fondo Crema:** `#FDFBF6` (fondo principal)
- **Texto Oscuro:** `#333333` (texto principal)
- **Dorado Acento:** `#D4A574` (elementos especiales)

## 📁 Estructura de Archivos

```
cremeria_raiz/
├── index.html              # Página principal
├── style.css               # Estilos CSS principales
├── script.js               # JavaScript e interacciones
├── config.php              # Configuración de base de datos
├── login.php               # Página de login
├── logout.php              # Cerrar sesión
├── dashboard.php           # Panel de administración
├── database_setup.sql      # Script SQL para crear BD
├── README.md               # Este archivo
└── images/                 # Carpeta para imágenes
    ├── logo-cremeria-raiz.png
    ├── hero-queso-artesanal.jpg
    ├── queso-oaxaca.jpg
    ├── queso-fresco.jpg
    ├── queso-requesón.jpg
    ├── queso-crema.jpg
    ├── queso-manchego.jpg
    ├── queso-doble-crema.jpg
    ├── historia-familia.jpg
    └── logo-cremeria-raiz-white.png
```

## 🚀 Guía de Instalación en Hostinger

### Paso 1: Preparar los Archivos
1. Descarga todos los archivos del proyecto
2. Comprime el contenido de la carpeta `cremeria_raiz/` en un archivo ZIP

### Paso 2: Subir Archivos a Hostinger
1. Accede al **Panel de Control de Hostinger**
2. Ve a **Administrador de Archivos**
3. Navega a la carpeta `public_html`
4. Sube y extrae el archivo ZIP
5. Asegúrate de que los archivos estén en la raíz de `public_html`

### Paso 3: Crear la Base de Datos
1. En el panel de Hostinger, ve a **Bases de Datos MySQL**
2. Crea una nueva base de datos llamada `cremeria_raiz`
3. Crea un usuario para la base de datos
4. Asigna todos los permisos al usuario
5. **Anota las credenciales:** nombre de BD, usuario y contraseña

### Paso 4: Configurar la Base de Datos
1. Ve a **phpMyAdmin** desde el panel de Hostinger
2. Selecciona tu base de datos `cremeria_raiz`
3. Ve a la pestaña **SQL**
4. Copia y pega el contenido de `database_setup.sql`
5. Ejecuta el script (botón "Continuar")

### Paso 5: Configurar la Conexión
1. Edita el archivo `config.php`
2. Cambia las siguientes líneas con tus datos:

```php
define('DB_HOST', 'localhost');           // Generalmente localhost en Hostinger
define('DB_NAME', 'tu_nombre_bd');        // Nombre de tu base de datos
define('DB_USER', 'tu_usuario_bd');       // Tu usuario de BD
define('DB_PASS', 'tu_password_bd');      // Tu contraseña de BD
```

### Paso 6: Crear Carpeta de Imágenes
1. Crea una carpeta llamada `images/` en `public_html`
2. Sube las imágenes de los productos (puedes usar imágenes de ejemplo)
3. Asegúrate de que las imágenes tengan los nombres correctos:
   - `logo-cremeria-raiz.png`
   - `hero-queso-artesanal.jpg`
   - `queso-oaxaca.jpg`, etc.

## 🔑 Credenciales de Acceso

### Panel de Administración
- **URL:** `tudominio.com/login.php`
- **Usuario:** `admin`
- **Contraseña:** `admin123`

> ⚠️ **IMPORTANTE:** Cambiar estas credenciales en producción por seguridad

## 📝 Funcionalidades del Dashboard

1. **Gestión de Productos:**
   - Agregar nuevos productos
   - Editar productos existentes
   - Eliminar productos
   - Visualizar estadísticas

2. **Características de Seguridad:**
   - Login con contraseñas hasheadas
   - Protección contra inyección SQL
   - Validación de formularios
   - Manejo seguro de sesiones

## 🎨 Personalización

### Cambiar Colores
Edita las variables CSS en `style.css`:
```css
:root {
    --primary-orange: #F26F21;  /* Color principal */
    --primary-blue: #1E3A8A;    /* Color secundario */
    --background-cream: #FDFBF6; /* Fondo */
    /* ... */
}
```

### Agregar Nuevas Secciones
1. Modifica `index.html` para agregar el HTML
2. Añade estilos en `style.css`
3. Incluye interacciones en `script.js` si es necesario

## 🔧 Mantenimiento

### Respaldos
- Realiza respaldos regulares de la base de datos desde phpMyAdmin
- Respalda los archivos del sitio periódicamente

### Actualizaciones de Seguridad
- Cambia las contraseñas regularmente
- Mantén actualizado PHP en tu hosting
- Revisa los logs de errores periódicamente

### Optimización
- Comprime las imágenes antes de subirlas
- Utiliza formatos WebP para mejor rendimiento
- Activa la compresión GZIP en el servidor

## 🐛 Solución de Problemas

### Error de Conexión a la Base de Datos
1. Verifica las credenciales en `config.php`
2. Asegúrate de que la base de datos existe
3. Confirma que el usuario tiene permisos

### Imágenes no se Muestran
1. Verifica que las imágenes estén en la carpeta `images/`
2. Confirma que los nombres coincidan exactamente
3. Revisa los permisos de la carpeta (755)

### Error 500
1. Revisa los logs de errores en el panel de Hostinger
2. Verifica la sintaxis de los archivos PHP
3. Asegúrate de que PHP esté configurado correctamente

## 📞 Soporte

### Documentación Adicional
- **HTML/CSS:** [MDN Web Docs](https://developer.mozilla.org/)
- **PHP:** [PHP.net](https://www.php.net/docs.php)
- **MySQL:** [MySQL Documentation](https://dev.mysql.com/doc/)
- **Hostinger:** [Base de Conocimientos](https://support.hostinger.com/)

### Recursos Útiles
- **Imágenes Gratuitas:** [Unsplash](https://unsplash.com/), [Pexels](https://www.pexels.com/)
- **Iconos:** [Font Awesome](https://fontawesome.com/)
- **Fuentes:** [Google Fonts](https://fonts.google.com/)

## 📜 Licencia

Este proyecto fue creado específicamente para Cremería Raíz. Todos los derechos reservados.

---

**Desarrollado con ❤️ para Cremería Raíz - El Auténtico Sabor de la Tradición Oaxaqueña**

📞 **Contacto para Soporte Técnico:** [Tu información de contacto]