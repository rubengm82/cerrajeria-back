# Cerrajeria ABP - Backend

Backend API REST para el proyecto de cerrajería (<a href="https://www.serralleriasolidaria.cat/" target="_blank">https://www.serralleriasolidaria.cat/</a>) desarrollado como trabajo académico ABP (Aprendizaje Basado en Proyectos).

## Autores

- **Alejandro Buenaventura Tarrillo**
- **Ruben Gallardo Mancha**

## Tecnologías

El proyecto está construido con las siguientes tecnologías:

### Backend
- **PHP 8.2+** - Lenguaje de programación del servidor
- **Laravel 12** - Framework PHP para desarrollo web
- **Laravel Sanctum** - Autenticación via tokens API
- **SQLite** - Base de datos ligera

### Frontend (asset building)
- **Vite** - Build tool moderna
- **Tailwind CSS 4** - Framework de estilos CSS
- **Axios** - Cliente HTTP para JavaScript

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- PHP 8.2 o superior
- Composer
- Node.js 18+ y npm
- Extensión PHP SQLite (pdo_sqlite)

## Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio>
cd cerrajeria-back
```

### 2. Instalar Dependencias PHP

```bash
composer install
```

### 3. Configurar Variables de Entorno

```bash
cp .env.example .env
```

### 4. Generar Clave de Aplicación

```bash
php artisan key:generate
```

### 5. Crear Base de Datos SQLite

El proyecto usa SQLite por defecto. Si no existe el archivo:

```bash
touch database/database.sqlite
```

### 6. Ejecutar Migraciones

```bash
php artisan migrate
```

### 7. (Opcional) Poblar Base de Datos

```bash
php artisan db:seed
```

### 8. Instalar Dependencias Node.js

```bash
npm install
```

### 9. Compilar Assets

```bash
npm run build
```

## Ejecutar el Proyecto

### Servidor de Desarrollo

```bash
php artisan serve
```

El servidor estará disponible en: `http://localhost:8000`

### Modo Desarrollo Completo (con Vite y Queue)

```bash
npm run dev
```

## Comandos Útiles

| Comando | Descripción |
|---------|-------------|
| `php artisan serve` | Iniciar servidor de desarrollo |
| `php artisan migrate` | Ejecutar migraciones de base de datos |
| `php artisan db:seed` | Poblar base de datos con datos de prueba |
| `php artisan test` | Ejecutar pruebas unitarias |
| `npm run dev` | Iniciar servidor Vite en modo desarrollo |
| `npm run build` | Compilar assets para producción |

## Estructura del Proyecto

```
cerrajeria-back/
├── app/
│   ├── Http/Controllers/  # Controladores de la API
│   └── Models/            # Modelos Eloquent
├── database/
│   ├── migrations/       # Migraciones de base de datos
│   └── seeders/          # Seeders para datos iniciales
├── routes/
│   └── api.php           # Rutas de la API
├── bootstrap/             # Archivos de arranque
├── config/                # Archivos de configuración
├── public/                # Archivos públicos
├── resources/             # Vistas y assets
├── storage/               # Archivos almacenados
└── tests/                 # Pruebas automatizadas
```
