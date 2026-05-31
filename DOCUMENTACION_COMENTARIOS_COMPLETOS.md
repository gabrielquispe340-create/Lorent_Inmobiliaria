# Documentación completa de archivos del proyecto

Este documento describe cada archivo relevante del proyecto y su función. Está pensado para ayudarte a entender qué hace cada carpeta y cada archivo del sistema.

---

## Raíz del proyecto

- `artisan`: comando de consola de Laravel para ejecutar migraciones, servidor local, tareas y otros comandos.
- `composer.json`: lista de dependencias PHP, autoload y scripts de Composer.
- `package.json`: lista de dependencias de frontend y scripts de Node/Vite.
- `README.md`: documentación general y descripción del proyecto.
- `phpunit.xml`: configuración de PHPUnit para pruebas.
- `vite.config.js`: configuración de Vite para compilar CSS y JS.
- `ESTRUCTURA_PROYECTO.md`: documento del proyecto con descripción de carpetas principales.
- `.env` / `.env.example`: configuración de entorno (base de datos, correo, clave de app). No se incluye en control de versiones.

## `bootstrap/`

- `app.php`: carga y configura la aplicación Laravel.
- `providers.php`: archivos y clases que se cargan al iniciar la aplicación.

## `app/`

Contiene la lógica principal de la aplicación.

### `app/Http/Controllers/`

- `Controller.php`: clase base de todos los controladores.
- `AuthController.php`: maneja inicio de sesión, registro, recuperación de contraseña y logout.
- `DashboardController.php`: muestra los paneles de control para cada rol (administrador, agente, asistente, cliente).
- `PropiedadController.php`: gestiona propiedades inmobiliarias: listados, creación, edición, eliminación y búsquedas.
- `UsuarioController.php`: gestiona usuarios del sistema, perfil y CRUD de usuarios.
- `SolicitudController.php`: gestiona solicitudes de visita, calendarios, estados de visita y clientes.
- `ReporteController.php`: genera o muestra reportes.

### `app/Http/Middleware/`

- `RoleMiddleware.php`: verifica el rol del usuario antes de permitir el acceso a rutas protegidas.
- `CheckRole.php`: middleware adicional para control de acceso por rol.

### `app/Models/`

- `User.php`: modelo de usuario estándar de Laravel (para autenticación native).
- `Usuario.php`: modelo personalizado para los usuarios del sistema con campos específicos como `rol` y `contrasena`.
- `Propiedad.php`: modelo de propiedades inmobiliarias.
- `SolicitudVisita.php`: modelo de solicitudes de visita de clientes.
- `RegistroActividad.php`: modelo que guarda registros de acciones del usuario.

### `app/Mail/`

- `PasswordResetMail.php`: plantilla y lógica para enviar correos de restablecimiento de contraseña.

### `app/Providers/`

- `AppServiceProvider.php`: configuración global y bindings de servicios para la aplicación.

## `config/`

Archivos de configuración de Laravel y del proyecto.

- `app.php`: configuración general de la aplicación, nombre, zona horaria y providers.
- `auth.php`: configuración de autenticación y guardias.
- `cache.php`: configuración del cache.
- `database.php`: configuración de conexión a bases de datos.
- `filesystems.php`: configuración de almacenamiento de archivos.
- `logging.php`: configuración de registros y canales de log.
- `mail.php`: configuración de correo.
- `queue.php`: configuración de colas.
- `services.php`: configuración de servicios externos (APIs, proveedores).
- `session.php`: configuración del manejo de sesión.

## `database/`

### `database/migrations/`

Migraciones que crean o modifican las tablas.

- `0001_01_01_000000_create_users_table.php`: tabla estándar de usuarios de Laravel.
- `0001_01_01_000001_create_cache_table.php`: tabla para cache de Laravel.
- `0001_01_01_000002_create_jobs_table.php`: tabla para trabajos en cola.
- `2025_05_05_100000_create_usuarios_table.php`: crea la tabla personalizada de usuarios del sistema.
- `2025_05_05_100001_create_propiedades_table.php`: crea tabla de propiedades inmobiliarias.
- `2025_05_05_100002_create_solicitudes_visita_table.php`: crea tabla de solicitudes de visita.
- `2025_05_05_100003_create_registros_actividad_table.php`: crea tabla de registros de actividad.
- `2026_05_10_225525_add_imagen_to_propiedades_table.php`: agrega columna de imagen a propiedades.
- `2026_05_12_000001_create_categorias_table.php`: crea tabla de categorías para propiedades.
- `2026_05_12_000002_create_propietarios_table.php`: crea tabla de propietarios.
- `2026_05_12_000003_add_columns_to_propiedades_table.php`: agrega columnas adicionales a propiedades.
- `2026_05_12_000004_create_contratos_table.php`: crea tabla de contratos.
- `2026_05_12_000005_create_prospectos_table.php`: crea tabla de prospectos.
- `2026_05_12_000006_create_seguimientos_table.php`: crea tabla de seguimientos.
- `2026_05_12_000007_create_resenas_table.php`: crea tabla de reseñas.

### `database/seeders/`

- `CreateAdminSeeder.php`: crea un usuario administrador inicial para el sistema.
- `DatabaseSeeder.php`: ejecuta los seeders para poblar datos iniciales.

### `database/factories/`

- `UserFactory.php`: fábrica para generar usuarios de prueba en las pruebas o seeders.

## `public/`

Archivos públicos que el navegador carga directamente.

- `index.php`: punto de entrada para todas las peticiones HTTP.

### `public/css/`

- `estilos.css`: estilos generales del frontend.
- `dashboard_usuario.css`: estilos específicos del dashboard de usuario.
- `dashboard.css`: estilos para paneles y dashboards.
- `compartido/topbar.css`: estilos compartidos para la barra superior.
- `auth/login.css`: estilos de las páginas de autenticación.
- `dashboard/dashboard.css`: estilo adicional de panel de control.

### `public/js/`

- `scrip.js`: scripts generales del frontend.
- `validaciones.js`: validaciones de formularios en el cliente.
- `compartido/topbar.js`: comportamiento de la barra superior.
- `auth/login.js`: scripts de la sección de autenticación.

## `resources/views/`

Vistas Blade que generan el HTML de cada página.

### `resources/views/auth/`
- `login.blade.php`: formulario de inicio de sesión.
- `registro.blade.php`: formulario de registro de nuevo usuario.
- `forgot-password.blade.php`: formulario para solicitar enlace de recuperación.
- `reset-password.blade.php`: formulario para ingresar nueva contraseña.

### `resources/views/admin/`
- `dashboard.blade.php`: panel principal del administrador.
- `propiedades.blade.php`: lista y gestión de propiedades desde admin.
- `usuarios.blade.php`: lista y gestión de usuarios desde admin.
- `buscar.blade.php`: búsqueda de datos dentro del panel admin.

### `resources/views/agente/`
- `dashboard.blade.php`: panel principal del agente.
- `propiedades.blade.php`: gestión de propiedades del agente.
- `buscar.blade.php`: página de búsqueda para agente.
- `clientes.blade.php`: lista de clientes y solicitudes asignadas al agente.
- `visitas.blade.php`: listado de solicitudes de visita del agente.
- `calendario.blade.php`: vista de calendario de visitas.

### `resources/views/asistente/`
- `dashboard.blade.php`: panel principal del asistente.
- `buscar.blade.php`: búsqueda de propiedades o solicitudes.
- `visitas.blade.php`: listado de visitas que maneja el asistente.
- `calendario.blade.php`: calendario para el asistente.

### `resources/views/cliente/`
- `dashboard.blade.php`: panel principal del cliente.
- `propiedades.blade.php`: muestra propiedades disponibles al cliente.
- `detalle.blade.php`: detalle de cada propiedad.
- `buscar.blade.php`: búsqueda de propiedades para cliente.
- `solicitudes.blade.php`: lista de solicitudes del cliente.
- `usuarios.blade.php`: vista de datos de usuario o clientes (dependiendo del diseño).
- `calendario.blade.php`: calendario de visitas del cliente.

### `resources/views/compartido/`
- `sidebar.blade.php`: menú lateral compartido entre varios dashboards.
- `perfil.blade.php`: información de perfil y edición de datos.
- `reportes.blade.php`: componentes reutilizables de reportes.

### `resources/views/layouts/`
- `app.blade.php`: plantilla base global de la aplicación.
- `panel.blade.php`: plantilla de panel de control y dashboards.

### `resources/views/emails/`
- `password_reset.blade.php`: plantilla del correo de recuperación de contraseña.

### `resources/views/error/`
- `403.blade.php`: página de error de acceso prohibido.

### `resources/views/errors/`
- `403.blade.php`: página alternativa de error 403.

### `resources/views/view/`
- `dashboard.blade.php`: vista adicional de dashboard o página de ejemplo.

- `welcome.blade.php`: página predeterminada de Laravel.

## `routes/`

- `web.php`: definición de todas las rutas web, agrupadas por autenticación y roles.
- `console.php`: definición de comandos Artisan personalizados.

## `tests/`

- `TestCase.php`: clase base para las pruebas PHPUnit.
- `Feature/ExampleTest.php`: ejemplo de prueba funcional.
- `Unit/ExampleTest.php`: ejemplo de prueba unitaria.

---

## Cómo usar esta documentación

- Si necesitas, puedo ahora anotar cada archivo con comentarios dentro del mismo código.
- También puedo crear un archivo `docs/` con comentarios línea por línea para los controladores y vistas clave.
- Para no modificar archivos de dependencias externas, este documento solo cubre los archivos del proyecto propio.
