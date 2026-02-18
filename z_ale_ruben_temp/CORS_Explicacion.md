# CORS en Nuestro Proyecto (Cerrajeria ABP)

## Estado Inicial

**Antes de la configuración:** El proyecto NO tenía CORS configurado. Esto significa:
- En local funcionaba "por suerte" (navegador permisivo o peticiones simples)
- En producción **fallaría** (frontend y backend en dominios distintos)

## Qué Se Hizo

### 1. Se publicó la configuración de CORS

```bash
php artisan config:publish cors
```

### 2. Se generó el archivo [`config/cors.php`](config/cors.php) con:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => false,
```

## Configuración Actual del Proyecto

| Parámetro | Valor | Significado |
|-----------|-------|-------------|
| `paths` | `api/*`, `sanctum/csrf-cookie` | Rutas con CORS enabled |
| `allowed_methods` | `*` | GET, POST, PUT, DELETE, etc. |
| `allowed_origins` | `*` | Cualquier dominio |
| `allowed_headers` | `*` | Cualquier header |
| `supports_credentials` | `false` | Sin cookies/auth |

## Cómo Afecta a Nuestro Frontend

### Si usas tokens (Sanctum Bearer Token):

```javascript
// Axios config
axios.get('/api/users', {
  headers: { Authorization: `Bearer ${token}` }
})
```

✅ Funcionará con la config actual porque no requiere credentials.

### Si usas cookies ( Sanctum SPA Auth):

```javascript
axios.get('/api/users', { withCredentials: true })
```

⚠️ Necesitarías cambiar `supports_credentials` a `true`.

## Siguientes Pasos

### Para Producción (cuando tengas el dominio):

Cambiar en [`config/cors.php`](config/cors.php:1):

```php
'allowed_origins' => [
    'https://tu-dominio-frontend.com',
    'https://www.tu-dominio-frontend.com',
],
```

### Para agregar nuevos dominios de desarrollo:

```php
'allowed_origins' => [
    'http://localhost:5173',  // Vite
    'http://localhost:3000', // React
    'http://127.0.0.1:5173',
    'https://tu-dominio-prod.com',
],
```

## Archivos Modificados

- [`config/cors.php`](config/cors.php) - **CREADO** - Configuración CORS

## Referencias

- [Laravel CORS Docs](https://laravel.com/docs/12.x/routing#cors)
