# Plan de organización técnica

## Objetivo
Crear una base mantenible para continuar iteraciones sin aumentar deuda técnica.

## Cambios aplicados

### 1) Capa de bootstrap y configuración
- Se centralizó el arranque en `includes/bootstrap.php`.
- Se agregaron constantes globales en `includes/config.php` (roles, login, URL API).

### 2) Capa de sesión y autorización
- `includes/auth.php` ahora expone funciones reutilizables para iniciar sesión y redirigir.
- `includes/authorization.php` centraliza lectura de sesión y validación de roles.

### 3) Cliente API robusto
- `includes/ConexionAPI.php` ahora tiene:
  - URL base configurable.
  - Métodos tipados.
  - Timeouts para evitar bloqueos largos.
  - Manejo explícito de JSON inválido.

### 4) Entradas principales alineadas
Se estandarizó el uso de helpers en:
- `index.php`
- `validacion-menu.php`
- `menu-admin.php`
- `menu-empresa.php`
- `logout.php`
- `user-admin.php`

## Próximos pasos sugeridos (prioridad)
1. **Alta**: extraer bloques de menú (acordeones) a archivos parciales reutilizables.
2. **Alta**: eliminar estilos inline y moverlos a `assets/css/` por módulo.
3. **Media**: crear carpeta `app/` para servicios de dominio y separar lógica de vista.
4. **Media**: agregar script de validación local (`php -l`) sobre archivos clave.
5. **Media**: definir naming convention de módulos (`snake-case` o `kebab-case`) y aplicarla.
