# Casos de Uso - Proyecto Cerrajeria ABP

## Modelo conceptual

El sistema es una aplicación de gestión para una cerrajería que incluye:
- Catálogo de productos (cerraduras, herramientas, complementos)
- Gestión de pedidos
- Soluciones personalizadas para casos especiales
- Sistema de usuarios con roles (cliente/admin)

---

## Caso de Uso 1: Compra de Productos

### Descripción
Un cliente autenticado puede navegar por el catálogo, seleccionar productos y crear un pedido.

### Actores
- **Actor principal**: Cliente registrado
- **Actor secundario**: Sistema (API)

### Flujo principal

1. El cliente inicia sesión en la aplicación (`POST /api/login`)
2. El sistema valida credenciales y retorna token de acceso
3. El cliente consulta el catálogo de productos (`GET /api/products`)
4. El sistema retorna lista de productos disponibles
5. El cliente filtra productos por categoría (`GET /api/categories/{id}`)
6. El sistema retorna productos de esa categoría
7. El cliente selecciona productos y crea un pedido (`POST /api/orders`)
8. El sistema crea el pedido y retorna confirmación

### Flujo alternativo

- **3a**. Si el cliente no está autenticado, puede ver productos públicos pero no puede comprar
- **7a**. El cliente puede agregar múltiples productos en un solo pedido

### Datos necesarios
- Token de autenticación (Bearer)
- Lista de IDs de productos
- Cantidades

### Endpoint relacionado
```
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
  "products": [
    {"product_id": 1, "quantity": 2},
    {"product_id": 5, "quantity": 1}
  ]
}
```

---

## Caso de Uso 2: Gestión del Catálogo de Productos

### Descripción
El administrador gestiona el catálogo de productos: crear, modificar, eliminar y restaurar productos.

### Actores
- **Actor principal**: Administrador
- **Actor secundario**: Sistema (API)

### Flujo principal

1. El administrador inicia sesión (`POST /api/login`)
2. El sistema valida credenciales de admin y retorna token
3. El administrador consulta productos existentes (`GET /api/products/with-trashed`)
4. El sistema retorna todos los productos (incluidos eliminados)
5. El administrador crea nuevo producto (`POST /api/products`)
6. El sistema crea el producto y retorna los datos
7. El administrador asocia imágenes al producto (`POST /api/product-images`)
8. El sistema guarda las imágenes

### Flujo alternativo

- **5a**. El administrador puede modificar un producto (`PUT /api/products/{id}`)
- **5b**. El administrador puede eliminar lógicamente (`DELETE /api/products/{id}`)
- **5c**. El administrador puede restaurar un producto eliminado (`POST /api/products/{id}/restore`)
- **5d**. El administrador puede eliminar permanentemente (`DELETE /api/products/{id}/force`)

### Datos necesarios
- Token de administrador
- Datos del producto (nombre, descripción, precio, categoría)
- Imágenes(opcionales)

### Endpoints relacionados
```
POST /api/products
GET /api/products/with-trashed
PUT /api/products/{id}
DELETE /api/products/{id}
POST /api/products/{id}/restore
DELETE /api/products/{id}/force
```

---

## Diagrama de casos de uso

```
┌─────────────────────────────────────────────────────────────┐
│                    SISTEMA CERRAJERIA                      │
├──────────────────────────────────────────────────���──────────┤
│                                                          │
│  ┌──────────────┐         ┌──────────────┐                │
│  │  Cliente    │         │ Administrador│                │
│  └──────┬─────┘         └──────┬──────┘                │
│         │                      │                         │
│    ┌────┴────┐          ┌────┴─────┐                    │
│    │ Ver     │          │ Gestionar│                    │
│    │ Catálogo│         │ Catálogo │                    │
│    └────┬────┘          └────┬─────┘                    │
│         │                      │                         │
│    ┌────┴────┐          ┌────┴─────┐                    │
│    │ Crear   │          │ Gestionar │                    │
│    │ Pedido │          │ Pedidos  │                     │
│    └────┬────┘          └────┬─────┘                    │
│         │                      │                         │
│    ┌────┴────┐          ┌────┴─────┐                    │
│    │ Solicitar│          │ Gestionar│                    │
│    │ Solución │          │ Usuarios │                    │
│    │Personal │          └──────────┘                    │
│    └────────┘                                           │
│                                                         │
└─────────────────────────────────────────────────────────────┘
```

---

## Caso de Uso 3: Seguimiento de Pedidos

### Descripción
Un cliente puede consultar el estado de sus pedidos y el administrador puede gestionarlos.

### Actores
- **Actor principal**: Cliente registrado / Administrador
- **Actor secundario**: Sistema (API)

### Flujo principal (Cliente)

1. El cliente inicia sesión (`POST /api/login`)
2. El sistema retorna token de acceso
3. El cliente consulta sus pedidos (`GET /api/orders`)
4. El sistema retorna lista de pedidos del cliente
5. El cliente consulta detalles de un pedido (`GET /api/orders/{id}`)
6. El sistema retorna detalles completos (productos, estado, fecha)

### Flujo principal (Administrador)

1. El admin inicia sesión (`POST /api/login`)
2. El admin consulta todos los pedidos (`GET /api/orders`)
3. El admin actualiza estado del pedido (`PUT /api/orders/{id}`)
4. El sistema actualiza el estado
5. El admin puede eliminar pedido si es necesario (`DELETE /api/orders/{id}`)

### Flujo alternativo

- **3a**. El cliente puede restaurar un pedido eliminado (`POST /api/orders/{id}/restore`)
- **3b**. El admin puede ver pedidos eliminados (`GET /api/orders/trashed`)

### Datos necesarios
- Token de autenticación
- ID del pedido
- Nuevo estado (pendiente, procesado, enviado, entregado, cancelado)

### Endpoints relacionados
```
GET /api/orders
GET /api/orders/{id}
PUT /api/orders/{id}
DELETE /api/orders/{id}
POST /api/orders/{id}/restore
```

---

## Caso de Uso 4: Solicitud de Solución Personalizada

### Descripción
Un cliente puede solicitar una solución personalizada para casos especiales (puertas con medidas no estándar, sistemas de seguridad complejos, etc.).

### Actores
- **Actor principal**: Cliente registrado
- **Actor secundario**: Administrador, Sistema (API)

### Flujo principal

1. El cliente inicia sesión (`POST /api/login`)
2. El cliente visualiza soluciones existentes públicas (`GET /api/custom-solutions`)
3. El sistema retorna lista de soluciones personalizadas disponibles
4. El cliente envía su solicitud (`POST /api/custom-solutions`)
5. El sistema registra la solicitud y retorna confirmación
6. El admin recibe notificación de nueva solicitud
7. El admin consulta todas las solicitudes (`GET /api/custom-solutions/with-trashed`)
8. El admin evalúa y responde a la solicitud

### Flujo alternativo

- **4a**. El cliente puede ver el estado de su solicitud
- **7a**. El admin puede marcar la solicitud como procesada
- **7b**. El admin puede eliminar una solicitud (`DELETE /api/custom-solutions/{id}`)
- **7c**. El admin puede restaurar solicitud eliminada (`POST /api/custom-solutions/{id}/restore`)

### Datos necesarios
- Token de autenticación
- Descripción del problema
- Medidas de la puerta/ventana
- Tipo de cerradura necesaria
- Fotos de referencia (opcional)

### Endpoints relacionados
```
GET /api/custom-solutions
POST /api/custom-solutions
GET /api/custom-solutions/{id}
PUT /api/custom-solutions/{id}
DELETE /api/custom-solutions/{id}
POST /api/custom-solutions/{id}/restore
GET /api/custom-solutions/trashed
```

---

## Matriz de trazabilidad

| Requisito funcional | Caso de uso |
|-------------------|-------------|
| RF01 - Catálogo de productos | Caso de uso 1, 2 |
| RF02 - Gestión de pedidos | Caso de uso 1, 3 |
| RF03 - Autenticación | Caso de uso 1, 2, 3, 4 |
| RF04 - Administración | Caso de uso 2, 3, 4 |
| RF05 - Soluciones personalizadas | Caso de uso 4 |