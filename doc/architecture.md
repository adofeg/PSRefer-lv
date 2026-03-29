# Arquitectura de Capas (Layered Architecture)

El sistema PS Refer utiliza una arquitectura de capas separada por perfiles de usuario, permitiendo que la lógica de Administradores y Asociados se mantenga aislada y segura.

## 👥 Perfiles de Usuario

### 1. Capa de Empleados (Employee Layer)
- **Roles**: `ADMIN`, `PSADMIN`.
- **Rutas**: Prefijadas con `/admin`.
- **Controladores**: Localizados en `App\Http\Controllers\Private\Admin`.
- **Políticas**: Localizadas en `App\Policies\Admin`.

### 2. Capa de Asociados (Associate Layer)
- **Roles**: `ASSOCIATE`.
- **Rutas**: Prefijadas con `/associate`.
- **Controladores**: Localizados en `App\Http\Controllers\Private\Associate`.
- **Políticas**: Localizadas en `App\Policies\Associate`.

## 🛡️ Descubrimiento Dinámico de Políticas

Para evitar la duplicación de lógica en el `AuthServiceProvider`, se implementó un sistema de descubrimiento basado en la ruta:
- Si la petición comienza con `/associate`, Laravel busca automáticamente la política en la carpeta `App\Policies\Associate`.
- De lo contrario, busca en `App\Policies\Admin`.

Esto permite que, por ejemplo, un Administrador pueda "Borrar" un Referido, pero un Asociado solo pueda "Ver" los suyos, usando el mismo modelo `Referral`.

## 🚀 Helpers del Modelo User
Se han añadido métodos útiles en `App\Models\User`:
- `isEmployee()`: Verifica si el usuario pertenece al staff.
- `isAssociate()`: Verifica si el usuario es un asociado externo.
- `isAdmin()`: Verifica específicamente el rol de Administrador.
