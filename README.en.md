<div align="center">

# 📦 Pedidos API

### B2B Order & Inventory Management System — REST Backend

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com)
[![Sanctum](https://img.shields.io/badge/Auth-Sanctum-3178C6?style=flat)](https://laravel.com/docs/sanctum)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat)](LICENSE)

*[Versión en español](README.md)*

</div>

---

A REST API built with Laravel to manage the full lifecycle of B2B orders: product catalog, business customers, real-time stock control, and an order state machine with controlled transitions. Designed as a pure backend (no views), meant to be consumed by a React frontend.

## 📑 Table of Contents

- [Stack](#-stack)
- [Features](#-features)
- [Technical Decisions](#-technical-decisions)
- [Installation](#-installation)
- [Endpoints](#-endpoints)
- [Roadmap](#-roadmap)

## 🛠 Stack

| Category | Technology |
|---|---|
| Framework | Laravel 13 |
| Database | MySQL |
| Authentication | Laravel Sanctum (tokens) |
| Roles & permissions | Spatie Laravel Permission |
| Testing *(planned)* | Pest |

## ✨ Features

- 🔐 **Token-based authentication** — register, login and logout with Sanctum
- 👥 **Role-based access** — admin, sales, warehouse and customer roles, with write operations kept separate from public reads
- 📦 **Product catalog** with categories and stock control
- 🧾 **Transactional order creation** — validates stock, decrements inventory and calculates the total inside a single atomic transaction
- 🔄 **State machine** — `pending → preparing → shipped → delivered`, with explicit transition validation (no skipping steps or going backwards)
- ↩️ **Order cancellation with stock rollback** — orders are never deleted, they're marked as `cancelled` and kept as a historical record
- ⚡ **Systematic eager loading** — no N+1 query issues on any relation-heavy listing

## 🧠 Technical Decisions

A few design choices worth explaining beyond the code itself:

- **The price is frozen on each order line** (`unit_price` in `order_items`) instead of being read live from `products.price`. This way, if a product's price changes later, historical orders remain unaffected.
- **Orders are never physically deleted.** Cancelling sets the `status` to `cancelled` and returns the stock — the historical record is always preserved, which is essential in any system with accounting implications.
- **Changing an order's status lives in its own endpoint** (`PATCH /orders/{id}/status`) rather than a generic `update`, so role-based permissions can be applied to that specific action and the transition logic stays in one place.
- **Every operation that involves multiple writes** (creating or cancelling an order) is wrapped in `DB::transaction()` — if anything fails midway, no partial data is left behind.

## 🚀 Installation

```bash
git clone https://github.com/gabrielsuarezdevv/pedidos-api.git
cd pedidos-api
composer install
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`, then:

```bash
php artisan migrate --seed
php artisan serve
```

## 📋 Endpoints

### Authentication

| Method | Endpoint | Description | Auth |
|---|---|---|:---:|
| POST | `/api/register` | User registration | ❌ |
| POST | `/api/login` | Login, returns a token | ❌ |
| POST | `/api/logout` | Revokes the current token | ✅ |

### Products

| Method | Endpoint | Description | Auth |
|---|---|---|:---:|
| GET | `/api/products` | List (public catalog) | ❌ |
| GET | `/api/products/{id}` | Details | ❌ |
| POST | `/api/products` | Create | ✅ |
| PUT | `/api/products/{id}` | Update | ✅ |
| DELETE | `/api/products/{id}` | Delete | ✅ |

### Customers

| Method | Endpoint | Description | Auth |
|---|---|---|:---:|
| GET | `/api/customers` | List | ❌ |
| GET | `/api/customers/{id}` | Details | ❌ |
| POST | `/api/customers` | Create | ✅ |
| PUT | `/api/customers/{id}` | Update | ✅ |
| DELETE | `/api/customers/{id}` | Delete | ✅ |

### Orders

| Method | Endpoint | Description | Auth |
|---|---|---|:---:|
| GET | `/api/orders` | List | ✅ |
| GET | `/api/orders/{id}` | Details | ✅ |
| POST | `/api/orders` | Create (validates stock, calculates total) | ✅ |
| PATCH | `/api/orders/{id}/status` | Change status | ✅ |
| DELETE | `/api/orders/{id}/cancel` | Cancel (returns stock) | ✅ |

<details>
<summary><strong>📦 Example: creating an order</strong></summary>

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

- [ ] Automated tests with Pest
- [ ] Metrics dashboard (sales by period, best-selling products)
- [ ] Real-time low-stock notifications (Laravel Reverb)
- [ ] React frontend

---

<div align="center">

Built by **Gabriel Suárez** — [GitHub](https://github.com/gabrielsuarezdevv)

</div>