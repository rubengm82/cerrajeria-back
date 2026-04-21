# Informe de Cascade en Base de Datos

## 📋 Resumen Ejecutivo

Este documento describe los cambios realizados en las relaciones de clave foránea (foreign keys) de la base de datos para cumplir con los requisitos de retención de datos y legalidad. Se han替换ado comportamientos `cascade` por `set null` en tablas donde la eliminación de datos podría causar pérdida de información importante.

---

## 🎯 Objetivo

- **Problema original**: Los cascade automáticos borraban datos importantes cuando se eliminaban registros padre (ej: usuario, categoría, producto).
- **Solución implementada**: Cambiar a `set null` para mantener los datos incluso cuando la entidad relacionada desaparece.
- **Beneficio**: Cumplimiento con regulaciones de retención de datos (GDPR, obligaciones fiscales).

---

## 📊 Tabla de Cascade Modificados

| Tabla Hijá | Campo | Tabla Padre | Comportamiento Anterior | Comportamiento Nuevo | Estado |
|------------|-------|-------------|------------------------|---------------------|--------|
| orders | user_id | users | cascade | set null | ✅ Modificado |
| products | category_id | categories | cascade | set null | ✅ Modificado |
| order_products | product_id | products | cascade | set null | ✅ Modificado |
| order_products | order_id | orders | cascade | set null | ✅ Modificado |
| product_features | product_id | products | cascade | set null | ✅ Modificado |
| product_features | feature_id | features | cascade | set null | ✅ Modificado |
| pack_products | pack_id | packs | cascade | set null | ✅ Modificado |
| pack_products | products_id | products | cascade | set null | ✅ Modificado |
| features | type_id | feature_types | cascade | set null | ✅ Modificado |

---

## 🔒 Tablas con Cascade Mantenidos (Sin Cambios)

Las siguientes tablas mantienen su comportamiento `cascade` porque los datos hijos solo tienen sentido vinculados al padre:

| Tabla Hijá | Campo | Tabla Padre | Justificación |
|------------|-------|-------------|----------------|
| product_images_files | product_id | products | Las imágenes solo existen para ese producto |
| pack_images_files | packs_id | packs | Las imágenes solo existen para ese pack |
| custom_solution_files | custom_solution_id | custom_solutions | Los archivos solo existen para esa solución |

---

## 📝 Detalle de Cambios por Entidad

### 1. Orders (Pedidos)

**Problema**: Si se eliminaba un usuario, se borraban todos sus pedidos.

**Solución**: 
```php
// Antes (peligroso):
$table->foreignId('user_id')->constrained('users')->onDelete('cascade');

// Después (seguro):
$table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
```

**Resultado**: Los pedidos históricos se conservan aunque el usuario sea eliminado. El campo `user_id` será `null`.

---

### 2. Products (Productos)

**Problema**: Si se eliminaba una categoría, se borraban todos los productos asociados.

**Solución**:
```php
// Antes:
$table->foreignId('category_id')->constrained('categories')->onDelete('cascade');

// Después:
$table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
```

**Resultado**: Los productos existen sin categoría. Se pueden reasignar manualmente.

---

### 3. Order Products (Línea de Pedidos)

**Problema**: Si se eliminaba un producto o pedido, se perdía el registro de qué productos se compraron.

**Solución**:
```php
// Antes:
$table->foreignId('product_id')->constrained('products')->onDelete('cascade');
$table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

// Después:
$table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
$table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
```

**Resultado**: El historial de compras se conserva incluso si el producto o pedido se elimina lógicamente.

---

### 4. Product Features (Características de Productos)

**Problema**: Si se eliminaba una característica de un producto, la característica desaparecía completamente.

**Solución**:
```php
// Antes:
$table->foreignId('product_id')->constrained('products')->onDelete('cascade');
$table->foreignId('feature_id')->constrained('features')->onDelete('cascade');

// Después:
$table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
$table->foreignId('feature_id')->nullable()->constrained('features')->onDelete('set null');
```

**Resultado**: Las características son reutilizables. Al quitarlas de un producto, la característica sigue existiendo para otros productos.

**Ejemplo práctico**: 
- Tienes una característica "Color: Negro" usada en 50 productos
- Quitas esa característica de 1 producto
- La característica "Color: Negro" sigue existiendo para los otros 49 productos

---

### 5. Pack Products (Productos en Packs)

**Problema**: Si se eliminaba un pack o producto, se perdía la asociación.

**Solución**:
```php
// Antes:
$table->foreignId('pack_id')->constrained('packs')->onDelete('cascade');
$table->foreignId('products_id')->constrained('products')->onDelete('cascade');

// Después:
$table->foreignId('pack_id')->nullable()->constrained('packs')->onDelete('set null');
$table->foreignId('products_id')->nullable()->constrained('products')->onDelete('set null');
```

**Resultado**: Las asociaciones entre packs y productos se mantienen aunque alguno de los dos se elimine.

---

### 6. Features (Características)

**Problema**: Si se eliminaba un tipo de característica, se borraban todas las características de ese tipo.

**Solución**:
```php
// Antes:
$table->foreignId('type_id')->constrained('feature_types')->onDelete('cascade');

// Después:
$table->foreignId('type_id')->nullable()->constrained('feature_types')->onDelete('set null');
```

**Resultado**: Las características existen sin tipo específico. Ejemplo: si eliminas el tipo "color", las características de color existentes quedan con `type_id = null`.

---

## 🛠️ Comandos para Aplicar los Cambios

Como la base de datos está en desarrollo y no hay datos, puedes recrear las tablas:

```bash
php artisan migrate:fresh
```

O si prefieres hacer una migración específica:

```bash
php artisan make:migration update_cascade_to_set_null
```

## 🗑️ SoftDeletes Implementados

Además de los cambios de cascade a set null, se ha implementado **SoftDeletes** en las siguientes entidades para permitir la restauración de registros eliminados:

| Entidad | Migración | Modelo | Estado |
|---------|-----------|--------|--------|
| Categories | 2026_02_26_0100 | Category.php | ✅ |
| Products | 2026_02_26_0200 | Product.php | ✅ |
| Packs | 2026_02_26_0400 | Pack.php | ✅ |
| Features | 2026_02_26_1000 | Feature.php | ✅ |
| Feature Types | 2026_02_26_0900 | FeatureType.php | ✅ |
| Orders | 2026_02_26_0700 | Order.php | ✅ |
| Custom Solutions | 2026_02_26_1200 | CustomSolution.php | ✅ |

### Cómo usar SoftDeletes

```php
// Eliminar (soft delete)
$product = Product::find(1);
$product->delete(); // No se borra, solo marca deleted_at

// Restaurar
$product->restore(); // Recupera el registro

// Ver registros eliminados
$products = Product::withTrashed()->get();

// Ver solo eliminados
$products = Product::onlyTrashed()->get();
```

### Excepciones

- **Users**: NO se implementa SoftDeletes. Si un usuario se da de baja, al registrarse de nuevo será un usuario totalmente nuevo (cuenta limpia, sin historial).

---

## 📚 Referencias

- **Laravel Documentation**: [Foreign Key Constraints](https://laravel.com/docs/10.x/migrations#foreign-key-constraints)
- **GDPR**: Los datos pueden conservarse si son necesarios para cumplimiento legal (albaranes, pedidos, etc.)
- **Best Practice**: Siempre considerar si los datos hijos deben sobrevivir a la eliminación del padre.

---

## ✅ Checklist de Verificación

- [x] orders -> user_id modificado
- [x] products -> category_id modificado
- [x] order_products -> product_id modificado
- [x] order_products -> order_id modificado
- [x] product_features -> product_id modificado
- [x] product_features -> feature_id modificado
- [x] pack_products -> pack_id modificado
- [x] pack_products -> products_id modificado
- [x] features -> type_id modificado

---

*Documento generado el: 2026-03-11*
*Proyecto: cerrajeria-back (Laravel)*
