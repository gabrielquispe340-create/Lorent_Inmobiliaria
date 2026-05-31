# Estructura del proyecto "Proyecto lorent"

Este documento describe las carpetas principales, los archivos clave y su función dentro del proyecto.

---

## Raíz del proyecto

- `artisan`: comando de consola de Laravel. Se usa para ejecutar migraciones, servidores, tareas y comandos personalizados.
- `composer.json`: define las dependencias PHP, scripts de Composer y la autoloading de clases.
- `package.json`: define dependencias y scripts de Node/Vite para activos de frontend.
- `.env`: configuración de entorno (base de datos, correo, variables de app). No debe compartirse en el repositorio.
- `README.md`: documentación general, aquí aparece la plantilla estándar de Laravel.
- `phpunit.xml`: configuración de pruebas unitarias.
- `vite.config.js`: configuración del empaquetador Vite para CSS y JS.

## `app/`

Contiene la lógica principal de la aplicación en PHP.

### `app/Http/Controllers/`
Controladores que reciben solicitudes HTTP y devuelven respuestas.

- `AuthController.php`: maneja login, registro, recuperación de contraseña y logout.
- `DashboardController.php`: muestra los dashboards según el rol (administrador, agente, asistente, cliente).
- `PropiedadController.php`: gestiona las propiedades: listado, creación, actualización, eliminación, búsqueda y detalles.
- `UsuarioController.php`: gestiona a los usuarios que el administrador puede crear, editar y borrar.
- `SolicitudController.php`: administra solicitudes de visitas, calendarios, clientes y estados de visitas.
- `ReporteController.php`: genera o muestra reportes para el administrador y el asistente.

### `app/Models/`
Modelos Eloquent que representan tablas de la base de datos.

- `User.php`: modelo de usuario principal de Laravel.
- `Usuario.php`: modelo adicional para datos de usuarios del sistema (posiblemente detalles de perfil o roles personalizados).
- `Propiedad.php`: modelo de propiedades inmobiliarias.
- `SolicitudVisita.php`: modelo de solicitudes de visita de clientes.
- `RegistroActividad.php`: modelo de registros de actividad en el sistema.

### `app/Mail/`
Clases que envían correos electrónicos.

- `PasswordResetMail.php`: correo para restablecimiento de contraseña.

### `app/Providers/`
Proveedores de servicio de Laravel.

- `AppServiceProvider.php`: sincronización de servicios, bindings, observadores, configuraciones globales.

## `bootstrap/`

- `bootstrap/app.php`: carga y prepara la aplicación Laravel.
- `bootstrap/cache/`: archivos generados en caché para acelerar el arranque.

## `config/`

Contiene configuración global de Laravel:

- `app.php`: configuración de aplicación.
- `auth.php`: configuración de autenticación.
- `database.php`: conexión y configuración de bases de datos.
- `mail.php`: configuración de correo.
- `queue.php`, `session.php`, `logging.php`, etc.

## `database/`

### `database/migrations/`
Archivos que crean y modifican las tablas.

- `create_usuarios_table.php`: tabla de usuarios personalizados.
- `create_propiedades_table.php`: tabla de propiedades.
- `create_solicitudes_visita_table.php`: tabla de solicitudes de visita.
- `create_registros_actividad_table.php`: tabla de registros de actividad.
- `create_categorias_table.php`, `create_propietarios_table.php`, `create_contratos_table.php`, `create_prospectos_table.php`, `create_seguimientos_table.php`, `create_resenas_table.php`: tablas adicionales para categorías, propietarios, contratos, prospectos, seguimientos y reseñas.
- `add_imagen_to_propiedades_table.php`, `add_columns_to_propiedades_table.php`: migraciones que extienden las tablas existentes.

### `database/seeders/`
Clases para poblar la base de datos con datos iniciales.

- `CreateAdminSeeder.php`: crea un usuario administrador inicial.
- `DatabaseSeeder.php`: ejecuta los seeders.

## `public/`

Archivos accesibles directamente desde el navegador.

- `index.php`: punto de entrada de Laravel.
- `css/`, `js/`: estilos y scripts públicos.
- `imagenes/`: archivos multimedia de la aplicación.

## `resources/`

### `resources/views/`
Vistas Blade que generan el HTML.

- `auth/`: pantallas de autenticación (login, registro, recuperación de contraseña, etc.).
- `admin/`, `agente/`, `asistente/`, `cliente/`: vistas específicas para cada rol.
- `compartido/`: vistas o componentes compartidos entre roles.
- `emails/`: plantillas de correo electrónico.
- `error/`, `errors/`: páginas de error.
- `layouts/`: plantillas base y diseño general.
- `view/`: vistas adicionales, posiblemente páginas aisladas.
- `welcome.blade.php`: página de bienvenida predeterminada de Laravel.

## `routes/`

- `web.php`: rutas públicas y protegidas de la aplicación. Aquí se definen las URL y su controlador correspondiente.
- `console.php`: comandos Artisan personalizados.

### Rutas principales en `routes/web.php`

- Rutas de autenticación: `/login`, `/registro`, `/forgot-password`, `/reset-password`.
- Rutas de administrador: prefijo `admin`, con acceso a dashboard, propiedades, usuarios y reportes.
- Rutas de agente: prefijo `agente`, con dashboard, propiedades, visitas, clientes y calendario.
- Rutas de asistente: prefijo `asistente`, con dashboard, búsqueda, visitas, reportes y calendario.
- Rutas de cliente: prefijo `cliente`, con dashboard, propiedades disponibles, detalle de propiedad, búsqueda, solicitudes y calendario.

## `storage/`

Archivos temporales y generados:

- `app/`: archivos subidos o generados por la aplicación.
- `framework/`: caché, sesiones y vistas compiladas.
- `logs/`: archivos de registro.

## `tests/`

Pruebas unitarias y de características.

- `TestCase.php`: clase base de pruebas.
- `Feature/`: pruebas de comportamiento del sistema.
- `Unit/`: pruebas unitarias de componentes individuales.

## Cómo usar este mapa

- Si quieres, puedo ahora comentar dentro de archivos específicos como `routes/web.php`, `app/Http/Controllers/AuthController.php` o `app/Models/Propiedad.php`.
- También puedo crear un `docs/` con comentarios línea por línea en los archivos clave.

---

### Recomendación

Para no modificar erróneamente todo el repositorio, te sugiero que empecemos con:

1. `routes/web.php`
2. `app/Http/Controllers/AuthController.php`
3. `app/Models/Propiedad.php`
4. `resources/views/auth/`

Así puedo explicarte el flujo completo de autenticación y la estructura de roles antes de comentar el resto.
