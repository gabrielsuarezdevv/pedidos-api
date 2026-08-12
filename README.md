<div align="center">

# 📦 Pedidos API

### Sistema de gestión de pedidos e inventario B2B — Backend REST

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com)
[![Sanctum](https://img.shields.io/badge/Auth-Sanctum-3178C6?style=flat)](https://laravel.com/docs/sanctum)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat)](LICENSE)

*[English version below](README.en.md)*

</div>

---

API REST construida con Laravel para gestionar el ciclo de vida completo de pedidos B2B: catálogo de productos, clientes empresa, control de stock en tiempo real y una máquina de estados de pedido con transiciones controladas. Diseñada como backend puro (sin vistas), pensada para ser consumida por un frontend en React.

## 📑 Índice

- [Stack](#-stack)
- [Funcionalidades](#-funcionalidades)
- [Decisiones técnicas](#-decisiones-técnicas)
- [Instalación](#-instalación)
- [Endpoints](#-endpoints)
- [Roadmap](#-roadmap)

## 🛠 Stack

| Categoría | Tecnología |
|---|---|
| Framework | Laravel 13 |
| Base de datos | MySQL |
| Autenticación | Laravel Sanctum (tokens) |
| Roles y permisos | Spatie Laravel Permission |
| Testing *(planeado)* | Pest |

## ✨ Funcionalidades

- 🔐 **Autenticación por tokens** — registro, login y logout con Sanctum
- 👥 **Roles diferenciados** — admin, comercial, almacén y cliente, con permisos de escritura separados de la lectura pública
- 📦 **Catálogo de productos** con categorías y control de stock
- 🧾 **Creación de pedidos transaccional** — valida stock, descuenta inventario y calcula el total en una única transacción atómica
- 🔄 **Máquina de estados** — `pending → preparing → shipped → delivered`, con validación explícita de transiciones (no se puede saltar pasos ni retroceder)
- ↩️ **Cancelación con devolución de stock** — sin borrar el pedido, se conserva como registro histórico (`cancelled`)
- ⚡ **Eager loading sistemático** — sin problemas N+1 en ningún listado con relaciones

## 🧠 Decisiones técnicas

Algunas decisiones de diseño que vale la pena explicar, más allá del código:

- **El precio se congela en cada línea de pedido** (`unit_price` en `order_items`), en vez de leerse en vivo desde `products.price`. Así, si el precio de un producto cambia, los pedidos históricos no se ven afectados retroactivamente.
- **Los pedidos nunca se borran físicamente.** Cancelar cambia el `status` a `cancelled` y devuelve el stock — el historial se conserva siempre, algo imprescindible en cualquier sistema con implicaciones contables.
- **Cambiar el estado de un pedido vive en un endpoint separado** (`PATCH /orders/{id}/status`) en lugar de un `update` genérico, para poder aplicar permisos por rol a esa acción concreta y mantener la lógica de transición en un único lugar.
- **Toda operación que combina varias escrituras** (crear pedido, cancelar pedido) está envuelta en `DB::transaction()` — si algo falla a mitad, no queda ningún dato a medias.

## 🚀 Instalación

```bash
git clone https://github.com/gabrielsuarezdevv/pedidos-api.git
cd pedidos-api
composer install
cp .env.example .env
php artisan key:generate
```

Configura tu base de datos en `.env`, luego:

```bash
php artisan migrate --seed
php artisan serve
```

## 📋 Endpoints

### Autenticación

| Método | Endpoint | Descripción | Auth |
|---|---|---|:---:|
| POST | `/api/register` | Registro de usuario | ❌ |
| POST | `/api/login` | Login, devuelve token | ❌ |
| POST | `/api/logout` | Revoca el token actual | ✅ |

### Productos

| Método | Endpoint | Descripción | Auth |
|---|---|---|:---:|
| GET | `/api/products` | Listado (catálogo público) | ❌ |
| GET | `/api/products/{id}` | Detalle | ❌ |
| POST | `/api/products` | Crear | ✅ |
| PUT | `/api/products/{id}` | Actualizar | ✅ |
| DELETE | `/api/products/{id}` | Eliminar | ✅ |

### Clientes

| Método | Endpoint | Descripción | Auth |
|---|---|---|:---:|
| GET | `/api/customers` | Listado | ❌ |
| GET | `/api/customers/{id}` | Detalle | ❌ |
| POST | `/api/customers` | Crear | ✅ |
| PUT | `/api/customers/{id}` | Actualizar | ✅ |
| DELETE | `/api/customers/{id}` | Eliminar | ✅ |

### Pedidos

| Método | Endpoint | Descripción | Auth |
|---|---|---|:---:|
| GET | `/api/orders` | Listado | ✅ |
| GET | `/api/orders/{id}` | Detalle | ✅ |
| POST | `/api/orders` | Crear (valida stock, calcula total) | ✅ |
| PATCH | `/api/orders/{id}/status` | Cambiar estado | ✅ |
| DELETE | `/api/orders/{id}/cancel` | Cancelar (devuelve stock) | ✅ |

<details>
<summary><strong>📦 Ejemplo: crear un pedido</strong></summary>

```json
POST /api/orders
Authorization: Bearer {token}

{
    "customer_id": 1,
    "items": [
        { "product_id": 1, "quantity": 5 },
        { "product_id": 2, "quantity": 2 }
    ]
}
```

</details>

## 🗺 Roadmap

- [ ] Tests automatizados con Pest
- [ ] Dashboard con métricas (ventas por periodo, productos más vendidos)
- [ ] Notificaciones en tiempo real de stock bajo (Laravel Reverb)
- [ ] Frontend en React

---

<div align="center">

Desarrollado por **Gabriel Suárez** — [GitHub](https://github.com/gabrielsuarezdevv)

</div>