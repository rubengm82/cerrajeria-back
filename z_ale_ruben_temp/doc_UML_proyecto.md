# UML del Proyecto - Controllers y Modelos

## Diagrama de Arquitectura General

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              API Routes                                     │
│  /api/products, /api/categories, /api/packs, /api/orders, /api/features...  │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                           Controllers                                       │
│                                                                             │
│  ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐              │
│  │ProductController│ │CategoryController│ │  PackController  │              │
│  └────────┬─────────┘ └────────┬─────────┘ └────────┬─────────┘              │
│           │                    │                    │                        │
│  ┌────────┴─────────┐ ┌────────┴─────────┐ ┌────────┴─────────┐              │
│  │ProductImageCtrl  │ │  OrderController  │ │ FeatureController │              │
│  └────────┬─────────┘ └────────┬─────────┘ └────────┬─────────┘              │
│           │                    │                    │                        │
│  ┌────────┴─────────┐ ┌────────┴─────────┐ ┌────────┴─────────┐              │
│  │PackImageController│ │CustomSolutionCtrl│ │ FeatureTypeCtrl │              │
│  └──────────────────┘ └──────────────────┘ └──────────────────┘              │
│                                                                             │
│  ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐              │
│  │  UserController │ │  AuthController  │ │VerificationCtrl  │              │
│  └──────────────────┘ └──────────────────┘ └──────────────────┘              │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                             Modelos                                          │
│                                                                             │
│  ┌────────────┐      ┌────────────┐      ┌────────────┐      ┌────────────┐  │
│  │    User    │◄────►│   Order    │◄────►│   Product  │◄────►│   Pack     │  │
│  └────────────┘      └────────────┘      └────────────┘      └────────────┘  │
│       │                    │                   │                   │         │
│       │                    │                   │                   │         │
│       │              ┌─────┴─────┐       ┌────┴────┐          ┌────┴────┐   │
│       │              │order_prod │       │  Pack   │          │  Pack   │   │
│       │              │ (pivot)   │       │Products │          │Images   │   │
│       │              └───────────┘       │ (pivot)│          └─────────┘   │
│       │                                   └────────┘                          │
│       │                   ┌───────────┐        │                             │
│       │                   │ Category  │◄───────┘                             │
│       │                   └───────────┘                                      │
│       │                         │                                             │
│       │                   ┌─────┴─────┐                                       │
│       │                   │ Product   │                                       │
│       │                   │Images     │                                       │
│       │                   └───────────┘                                      │
│       │                                                                    │
│       │                   ┌─────────────┐      ┌─────────────┐              │
│       └──────────────────►│FeatureType │◄────►│   Feature   │              │
│                           └─────────────┘      └─────────────┘              │
│                                  │                      │                   │
│                                  │                ┌─────┴─────┐              │
│                                  │                │  Product  │              │
│                                  │                │Features   │              │
│                                  │                │ (pivot)   │              │
│                                  │                └───────────┘              │
│                                                                             │
│  ┌──────────────────┐                                                      │
│  │  CustomSolution  │                                                      │
│  └──────────────────┘                                                      │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Controller -> Modelo

### API Controllers

| Controller | Modelo | Métodos |
|------------|--------|---------|
| `ProductController` | `Product` | index, indexWithTrashed, store, show, update, destroy, trashed, restore, forceDelete, getImportantProducts |
| `CategoryController` | `Category` | index, indexWithTrashed, store, show, update, destroy, trashed, restore, forceDelete, getImportantCategories |
| `PackController` | `Pack` | index, indexWithTrashed, store, show, update, destroy, trashed, restore, forceDelete |
| `OrderController` | `Order` | index, store, show, update, destroy, trashed, restore, forceDelete |
| `FeatureController` | `Feature` | index, indexWithTrashed, store, show, update, destroy, trashed, restore, forceDelete |
| `FeatureTypeController` | `FeatureType` | index, indexWithTrashed, store, show, update, destroy, trashed, restore, forceDelete |
| `CustomSolutionController` | `CustomSolution` | index, indexWithTrashed, store, show, update, destroy, trashed, restore, forceDelete |
| `ProductImageController` | `ProductImageFile` | index, store, show, update, destroy |
| `PackImageController` | `PackImageFile` | index, store, show, update, destroy |

### Controllers No-API

| Controller | Modelo | Métodos |
|------------|--------|---------|
| `UserController` | `User` | index, store, show, update, destroy |
| `AuthController` | `User` | register, login, logout, user, resendVerificationEmail |
| `VerificationController` | - | - (verificación email) |
| `PasswordResetController` | - | - (reset password) |

---

## Modelos - Detalle de Atributos y Relaciones

### User
```
+----------------------------+
|         User               |
+----------------------------+
| - id: int                  |
| - name: string             |
| - last_name_one: string    |
| - last_name_second: string?|
| - dni: string              |
| - phone: string            |
| - email: string           |
| - address: string         |
| - zip_code: string         |
| - password: string        |
| - role: string (admin/user)|
| - email_verified_at: datetime|
+----------------------------+
| + orders(): HasMany        |
+----------------------------+
```

### Product
```
+----------------------------+
|        Product             |
+----------------------------+
| - id: int                  |
| - name: string            |
| - description: text?      |
| - price: decimal          |
| - stock: int              |
| - code: string            |
| - discount: decimal?     |
| - category_id: int        |
| - is_installable: bool     |
| - is_important_to_show: bool|
| - installation_price: decimal?|
| - extra_keys: int?         |
| - is_active: bool          |
+----------------------------+
| + category(): BelongsTo   |
| + images(): HasMany        |
| + orders(): BelongsToMany  |
| + packs(): BelongsToMany   |
| + features(): BelongsToMany|
+----------------------------+
```

### Category
```
+----------------------------+
|       Category             |
+----------------------------+
| - id: int                  |
| - name: string            |
| - is_important_to_show: bool|
| - image: string           |
+----------------------------+
| + products(): HasMany      |
+----------------------------+
```

### Pack
```
+----------------------------+
|          Pack              |
+----------------------------+
| - id: int                  |
| - name: string            |
| - total_price: int        |
| - description: text?      |
+----------------------------+
| + products(): BelongsToMany|
| + images(): HasMany        |
+----------------------------+
```

### Order
```
+----------------------------+
|          Order             |
+----------------------------+
| - id: int                  |
| - user_id: int             |
| - status: string           |
| - installation_address: string|
| - shipping_address: string|
| - shipped_at: datetime?    |
| - payment_method: string  |
+----------------------------+
| + user(): BelongsTo        |
| + products(): BelongsToMany|
+----------------------------+
```

### FeatureType
```
+----------------------------+
|       FeatureType          |
+----------------------------+
| - id: int                  |
| - name: string            |
+----------------------------+
| + features(): HasMany      |
+----------------------------+
```

### Feature
```
+----------------------------+
|         Feature            |
+----------------------------+
| - id: int                  |
| - type_id: int             |
| - value: string            |
+----------------------------+
| + type(): BelongsTo        |
| + products(): BelongsToMany|
+----------------------------+
```

### CustomSolution
```
+----------------------------+
|     CustomSolution         |
+----------------------------+
| - id: int                  |
| - email: string            |
| - phone: string           |
| - description: text        |
| - status: string (pending/closed)|
+----------------------------+
```

### ProductImageFile
```
+----------------------------+
|    ProductImageFile        |
+----------------------------+
| - id: int                  |
| - product_id: int          |
| - path: string             |
| - is_important: bool       |
+----------------------------+
| + product(): BelongsTo      |
+----------------------------+
```

### PackImageFile
```
+----------------------------+
|      PackImageFile         |
+----------------------------+
| - id: int                  |
| - packs_id: int            |
| - path: string             |
| - is_important: bool       |
+----------------------------+
| + pack(): BelongsTo         |
+----------------------------+
```

---

## Relaciones entre Modelos (Resumen)

```
User (1) ──────────► (N) Order
                              │
                              │
                              ▼
                            (N) ◄──────────── (N) Product
                                              │
                                              ├──► (1) Category
                                              │
                                              ├──► (N) ProductImageFile
                                              │
                                              ├──► (N) Feature (many-to-many via product_features)
                                              │
                                              └──► (N) Pack (many-to-many via pack_products)

Pack (1) ◄────────── (N) PackImageFile
        │
        └──► (N) Product (many-to-many)

Feature (N) ◄─────── (1) FeatureType
```

---

## Tabla Pivot

| Tabla Pivot | Modelos | Atributos adicionales |
|-------------|---------|----------------------|
| `order_products` | Order ↔ Product | quantity |
| `pack_products` | Pack ↔ Product | - |
| `product_features` | Product ↔ Feature | - |

---

## Notas

- Todos los modelos principales usan `SoftDeletes` (eliminación lógica)
- Los Controllers API siguen el patrón RESTful estándar
- Los controladores soportan operaciones CRUD completas + gestión de papelera
- La autenticación usa Laravel Sanctum (tokens API)
- Todos los usuarios implementan verificación de email (`MustVerifyEmail`)