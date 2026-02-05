# Compatibilidad con PostgreSQL en Producción

**Respuesta a tu pregunta:**
> "si yo llevo este proyecto a un servidor con postgress funcionaria?"

**SÍ, funcionará perfectamente.**

Laravel está diseñado par ser agnóstico a la base de datos gracias a **Eloquent ORM**. Esto significa que el código que escribimos (por ejemplo, `User::all()` o `Referral::create(...)`) funciona igual en SQLite, MySQL y PostgreSQL.

### Puntos Clave para el Servidor de Producción:
1.  **Drivers**: El servidor debe tener instalado `php-pgsql`.
2.  **Configuración**: Solo necesitarás cambiar las variables en el archivo `.env` del servidor:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=tu_servidor_postgres
    DB_PORT=5432
    DB_DATABASE=nombre_bd
    DB_USERNAME=usuario
    DB_PASSWORD=contraseña
    ```
3.  **Migraciones**: Al ejecutar `php artisan migrate`, Laravel creará automáticamente las tablas con los tipos de datos correctos para PostgreSQL.

### Lo que haremos ahora localmente:
Para que te funcione **YA** en tu máquina sin complicarnos con la configuración de tu PostgreSQL local (puerto 5433), voy a configurar **SQLite**. Esto crea la base de datos en un simple archivo dentro de tu proyecto. Esto **NO** afecta la compatibilidad con PostgreSQL en el futuro; es solo para desarrollo local.
