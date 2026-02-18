# Configuración API Laravel 12 - Resumen

## Resumen de lo realizado

Este documento describe todos los cambios realizados para configurar un proyecto Laravel 12 con soporte para API REST.

---

## 1. Instalación inicial

### Comando ejecutado
```bash
php artisan install:api
```

### Qué se instaló
- **Paquete**: `laravel/sanctum` v4.3.1
- **Migración**: `create_personal_access_tokens_table` (tabla para tokens de API)

---

## 2. Archivos creados

### app/Http/Controllers/AuthController.php
Controlador de autenticación con tres métodos:
- `login(Request $request)` - Inicia sesión y retorna token
- `logout(Request $request)` - Cierra sesión (revoca tokens)
- `user(Request $request)` - Obtiene el usuario autenticado

### routes/api.php
Archivo de rutas API:
```php
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

---

## 3. Archivos modificados

### app/Models/User.php
Se agregó el trait `HasApiTokens` para permitir autenticación via tokens:
```php
use Laravel\Sanctum\HasApiTokens;

// En la clase:
use HasFactory, Notifiable, HasApiTokens;
```

### database/seeders/UsersSeeder.php
Se corrigió para que la contraseña esté hasheada:
```php
use Illuminate\Support\Facades\Hash;

'password' => Hash::make('admin')
```

También se cambió `User::create()` por `updateOrCreate()` para evitar duplicados.

### bootstrap/app.php
Se agregó configuración para API:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(prepend: [
        \Illuminate\Session\Middleware\StartSession::class,
    ]);
})
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->shouldRenderJsonWhen(function () {
        return true;
    });
})
```

---

## 4. Rutas disponibles

| Método | Endpoint | Descripción | Autenticación |
|--------|----------|-------------|---------------|
| POST | `/api/login` | Iniciar sesión | No |
| POST | `/api/logout` | Cerrar sesión | Sí (Bearer Token) |
| GET | `/api/user` | Obtener usuario | Sí (Bearer Token) |

---

## 5. Cómo usar la API

### 1. Iniciar sesión
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@email.com","password":"admin"}'
```

**Respuesta:**
```json
{
  "message": "Login exitoso",
  "user": {
    "id": 1,
    "name": "admin",
    "email": "admin@email.com"
  },
  "token": "1|xxxxxxxxxxxxx"
}
```

### 2. Usar el token
```bash
curl -X GET http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer TU_TOKEN"
```

### 3. Cerrar sesión
```bash
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer TU_TOKEN"
```

---

## 6. Usuario de prueba

- **Email**: admin@email.com
- **Password**: admin

---

## 7. Archivos de configuración de Sanctum

Para publicar la configuración de Sanctum:
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Esto crea `config/sanctum.php`.

---

## 8. Notas importantes

- Las rutas API tienen el prefijo `/api/`
- El token de autenticación se debe enviar en el header: `Authorization: Bearer TOKEN`
- Los errores de autenticación retornan JSON con código 401
- La tabla `personal_access_tokens` ya fue creada automáticamente
