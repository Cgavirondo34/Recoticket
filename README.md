# 🎫 RecoTicket

**Plataforma SaaS de venta de entradas para eventos** — construida con Laravel 13, Tailwind CSS y SQLite/PostgreSQL.

> Para la documentación completa (roles, flujo de compra, integración con Mercado Pago, etc.) consultá el [📖 Manual de Uso](MANUAL_DE_USO.md).

---

## Requisitos previos

| Herramienta | Versión mínima |
|-------------|---------------|
| PHP | 8.3 |
| Composer | 2.x |
| Node.js + npm | 18+ |
| Base de datos | SQLite (dev) · PostgreSQL 15+ (prod) |

---

## ▶️ Cómo ejecutar el proyecto localmente

### 1. Clonar el repositorio

```bash
git clone https://github.com/Cgavirondo34/Recoticket.git
cd Recoticket
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Crear y migrar la base de datos (SQLite)

```bash
touch database/database.sqlite
php artisan migrate --seed
```

> El flag `--seed` carga usuarios y datos de demo. Podés omitirlo si no los necesitás.

### 5. Compilar los assets y levantar el servidor

```bash
npm run build
php artisan serve
```

Abrí tu navegador en **http://localhost:8000** 🎉

---

### Inicio rápido (un solo comando)

Si preferís hacerlo todo de una vez:

```bash
composer run setup
php artisan serve
```

---

## 👤 Usuarios de demo

Luego de ejecutar `--seed`:

| Email | Contraseña | Rol |
|-------|-----------|-----|
| `admin@recoticket.com` | `password` | Administrador |
| `organizer@recoticket.com` | `password` | Organizador |
| `buyer@recoticket.com` | `password` | Comprador |

---

## 💳 Pago con Mercado Pago

En entorno local (`APP_ENV=local`) el pago se **simula automáticamente** — no hace falta configurar credenciales de MP para probar el flujo completo.

Para un entorno de producción, agregá al `.env`:

```dotenv
MP_PUBLIC_KEY=TEST-xxxx
MP_ACCESS_TOKEN=TEST-xxxx
MP_WEBHOOK_SECRET=tu_secreto
```

---

## 📄 Licencia

MIT
