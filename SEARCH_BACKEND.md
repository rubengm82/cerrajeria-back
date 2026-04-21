# Backend Installation - Search Feature

## Files Added

### 1. Controller
`app/Http/Controllers/Api/SearchController.php`

**Routes:**
- `GET /api/search` → `SearchController@search`
- `GET /api/search/quick` → `SearchController@quickSearch`

### 2. Migration
`database/migrations/2026_04_18_140000_add_search_indexes.php`

**Indexes created:**
- MySQL FULLTEXT on `products(name, description, sku)`
- MySQL FULLTEXT on `packs(name, description)`
- Regular indexes:
  - `products`: category_id, is_active, is_important_to_show, price, name
  - `packs`: total_price, name
  - `pack_products`: unique(products_id, pack_id), indexes on both FK

### 3. Routes
Added to `routes/api.php`:
```php
use App\Http\Controllers\Api\SearchController;

Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/quick', [SearchController::class, 'quickSearch']);
```

## Installation Steps

```bash
cd /home/ruben/LaravelPRJ/cerrajeria-abp/cerrajeria-back

# 1. Run migrations
php artisan migrate

# 2. Verify routes
php artisan route:list | grep search

# Expected output:
# GET|HEAD  api/search .................... SearchController@search
# GET|HEAD  api/search/quick .............. SearchController@quickSearch
```

## API Documentation

### GET /api/search

**Query params:**
- `q` (required, min 2 chars)
- `category_id` (optional, integer)
- `price_min` (optional, float)
- `price_max` (optional, float)
- `only_packs` (optional, boolean)
- `limit` (optional, integer, default 20)

**Example:**
```
GET http://localhost:8000/api/search?q=llave&limit=20&category_id=1&price_min=10
```

**Response:**
```json
{
  "products": [
    {
      "id": 1,
      "name": "Llave inglesa",
      "price": "12.99",
      "description": "...",
      "category": {"id": 1, "name": "Herramientas"},
      "images": [{"id": 1, "path": "products/1/img.jpg", "is_important": true}],
      "features": []
    }
  ],
  "packs": [
    {
      "id": 5,
      "name": "Kit de herramientas",
      "total_price": "49.99",
      "description": "...",
      "products_count": 5,
      "products": [
        {"id": 1, "name": "Llave", "price": "12.99", "image": "..."}
      ],
      "images": [{"id": 2, "path": "packs/5/img.jpg"}]
    }
  ],
  "suggestions": ["llave", "inglesa"],
  "categories": [
    {"id": 1, "name": "Herramientas", "products_count": 15}
  ],
  "total": 23
}
```

### GET /api/search/quick

**Query params:**
- `q` (required, min 2 chars)
- `limit` (optional, default 5)

**Response:**
```json
{
  "products": [
    {
      "id": 1,
      "name": "Llave",
      "price": "12.99",
      "category_id": 1,
      "category": {"id": 1, "name": "Herramientas"},
      "images": [{"path": "..."}],
      "discount": null,
      "stock": 50
    }
  ],
  "packs": [
    {
      "id": 5,
      "name": "Kit",
      "price": "49.99",
      "products_count": 5,
      "images": [{"path": "..."}]
    }
  ]
}
```

## Database Indexes

**Products table:**
- FULLTEXT `products_search_idx` on (name, description, sku)
- INDEX `category_id`
- INDEX `is_active`
- INDEX `is_important_to_show`
- INDEX `price`
- INDEX `name`

**Packs table:**
- FULLTEXT `packs_search_idx` on (name, description)
- INDEX `total_price`
- INDEX `name`

**Pack_products pivot:**
- UNIQUE `pack_product_unique` on (products_id, pack_id)
- INDEX `products_id`
- INDEX `pack_id`

## Performance Notes

For ~100 products, MySQL FULLTEXT indexes provide excellent performance. Queries execute in milliseconds.

If using PostgreSQL, replace `DB::statement('ALTER TABLE ... ADD FULLTEXT ...')` with:
```php
DB::statement('CREATE INDEX products_search_idx ON products USING gin(to_tsvector(\'spanish\', name || \' \' || description || \' \' || sku))');
```

## Troubleshooting

**Error: "SQLSTATE[42000]: Syntax error or access violation: 1064"**
- MySQL version < 5.6 doesn't support FULLTEXT on InnoDB. Use `->index()` on columns instead.

**Migration fails because indexes already exist:**
- Rollback: `php artisan migrate:rollback`
- Manually drop indexes in MySQL if needed.

**Search returns no results:**
- Check data exists: `SELECT * FROM products WHERE name LIKE '%llave%'`
- Verify indexes: `SHOW INDEX FROM products;`
