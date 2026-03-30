# SST-Manager (Frontend PHP)

Proyecto frontend en PHP para gestión SST, conectado vía API HTTP al backend.

## Estructura recomendada

- `index.php`: login y creación de sesión.
- `validacion-menu.php`: selector de panel por rol.
- `menu-admin.php`: menú principal para administración.
- `menu-empresa.php`: menú principal por empresa.
- `includes/`: bootstrap, configuración global, autenticación/autorización y cliente API.
- `pages/` y `pages-empresa/`: vistas por dominio funcional.
- `assets/`: estilos e imágenes.
- `docs/`: documentación técnica y convenciones.

## Flujo de autenticación

1. El usuario inicia sesión en `index.php`.
2. Se guarda token + datos de perfil en `$_SESSION`.
3. Cada entrada protegida usa funciones de `includes/bootstrap.php`:
   - `requireAuthenticatedSession()`
   - `requireRole([...])`
4. Cierre de sesión centralizado en `logout.php`.

## Convenciones de mantenimiento

- Evitar `session_start()` duplicado en páginas de entrada; usar `startSessionIfNeeded()`.
- Evitar validaciones de rol manuales; usar `requireRole()` y constantes de `includes/config.php`.
- Usar `ConexionAPI` para peticiones HTTP (timeouts y parseo JSON centralizado).
- Mantener lógica de negocio fuera de HTML cuando sea posible.

## Siguiente fase recomendada

- Migrar menús y layouts repetidos a componentes parciales (`includes/views/`).
- Incorporar pruebas de smoke con `php -l` en CI.
- Estandarizar nombres de rutas para módulos de empresa y administración.
