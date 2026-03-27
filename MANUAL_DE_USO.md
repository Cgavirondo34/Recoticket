# 📖 Manual de Uso — RecoTicket

**Plataforma SaaS de venta de entradas para eventos**
Versión 1.0 · Laravel 13 · PostgreSQL / SQLite

---

## Índice

1. [Descripción general](#1-descripción-general)
2. [Requisitos del sistema](#2-requisitos-del-sistema)
3. [Instalación y configuración](#3-instalación-y-configuración)
4. [Variables de entorno](#4-variables-de-entorno)
5. [Roles de usuario](#5-roles-de-usuario)
6. [Credenciales de demo](#6-credenciales-de-demo)
7. [Guía por rol](#7-guía-por-rol)
   - 7.1 [Comprador (Buyer)](#71-comprador-buyer)
   - 7.2 [Organizador](#72-organizador)
   - 7.3 [Administrador](#73-administrador)
8. [Flujo completo de compra](#8-flujo-completo-de-compra)
9. [Integración con Mercado Pago](#9-integración-con-mercado-pago)
10. [Escaneo de entradas (QR)](#10-escaneo-de-entradas-qr)
11. [Estructura del proyecto](#11-estructura-del-proyecto)
12. [Referencia de rutas](#12-referencia-de-rutas)

---

## 1. Descripción general

**RecoTicket** es una plataforma de venta de entradas en línea similar a Eventbrite, diseñada para el mercado argentino. Permite a los organizadores crear eventos con distintos tipos de entrada, a los compradores adquirirlas con Mercado Pago y a los porteros escanear códigos QR el día del evento.

### Funcionalidades principales

| Módulo | Descripción |
|--------|-------------|
| 🎫 Eventos públicos | Exploración de eventos por categoría y búsqueda |
| 🛒 Compra de entradas | Carrito, checkout y pago via Mercado Pago |
| 📄 Entradas PDF/QR | Cada entrada tiene código único + QR descargable |
| 🎪 Panel organizador | Crear/editar eventos, tipos de entrada, estadísticas |
| 📲 Escáner QR | Validación de entradas en puerta desde cualquier dispositivo |
| 🛡 Panel admin | Gestión de usuarios, organizadores y eventos de la plataforma |

---

## 2. Requisitos del sistema

- **PHP** 8.3 o superior
- **Composer** 2.x
- **Node.js** 18+ y npm (para assets)
- **Base de datos**: PostgreSQL 15+ (recomendado para producción) o SQLite (desarrollo)
- **Extensiones PHP**: `pdo_pgsql`, `pdo_sqlite`, `gd`, `mbstring`, `xml`, `zip`

---

## 3. Instalación y configuración

### Clonar e instalar dependencias

```bash
git clone https://github.com/Cgavirondo34/Recoticket.git
cd Recoticket

# Instalar dependencias PHP
composer install

# Instalar dependencias JS
npm install

# Copiar archivo de configuración
cp .env.example .env

# Generar clave de la aplicación
php artisan key:generate
```

### Configurar la base de datos

**Opción A — SQLite (desarrollo rápido)**

```bash
touch database/database.sqlite
# En .env asegurarse de que:
# DB_CONNECTION=sqlite
```

**Opción B — PostgreSQL (producción)**

```bash
# Crear la base de datos en PostgreSQL
createdb recoticket

# En .env configurar:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=recoticket
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña
```

### Ejecutar migraciones y seeders

```bash
# Crear tablas y cargar datos de ejemplo
php artisan migrate --seed

# Solo migrar (sin datos de ejemplo)
php artisan migrate
```

### Compilar assets y levantar el servidor

```bash
# Compilar assets
npm run build

# Levantar servidor de desarrollo
php artisan serve
```

La aplicación estará disponible en: **http://localhost:8000**

### Inicio rápido (un solo comando)

```bash
composer run setup
```

---

## 4. Variables de entorno

Editar el archivo `.env` con los valores correspondientes:

```dotenv
# Aplicación
APP_NAME="RecoTicket"
APP_ENV=local           # local | production
APP_KEY=                # se genera con php artisan key:generate
APP_DEBUG=true          # false en producción
APP_URL=http://localhost:8000

# Base de datos (SQLite para desarrollo)
DB_CONNECTION=sqlite

# Base de datos (PostgreSQL para producción)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=recoticket
# DB_USERNAME=postgres
# DB_PASSWORD=

# Mercado Pago
MP_PUBLIC_KEY=          # Clave pública de tu cuenta MP
MP_ACCESS_TOKEN=        # Access token de tu cuenta MP
MP_WEBHOOK_SECRET=      # Secreto para validar webhooks (opcional)
```

> ⚠️ **Importante**: En `APP_ENV=local`, el pago se simula automáticamente (sin necesidad de credenciales MP reales). En producción, se requieren las credenciales de Mercado Pago.

---

## 5. Roles de usuario

La plataforma tiene tres roles diferenciados:

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| `buyer` | Comprador (rol por defecto al registrarse) | `/buyer/*` |
| `organizer` | Organizador de eventos | `/organizer/*` |
| `admin` | Administrador de la plataforma | `/admin/*` |

El rol se asigna al momento del registro (por defecto `buyer`). El administrador puede cambiar el rol de cualquier usuario desde el panel de administración.

---

## 6. Credenciales de demo

Luego de ejecutar `php artisan migrate --seed`, se crean los siguientes usuarios de prueba:

| Email | Contraseña | Rol |
|-------|-----------|-----|
| `admin@recoticket.com` | `password` | Administrador |
| `organizer@recoticket.com` | `password` | Organizador |
| `buyer@recoticket.com` | `password` | Comprador |

---

## 7. Guía por rol

### 7.1 Comprador (Buyer)

#### Registrarse / Iniciar sesión

1. Ir a **http://localhost:8000/register**
2. Completar nombre, email y contraseña
3. El sistema crea la cuenta con rol `buyer` automáticamente

#### Explorar y buscar eventos

1. Ir a la página principal **http://localhost:8000**
2. Usar la barra de búsqueda para filtrar por nombre de evento
3. Filtrar por categoría usando los botones de categoría
4. Hacer clic en una tarjeta de evento para ver los detalles

#### Comprar una entrada

1. En la página del evento, seleccionar el tipo de entrada deseado
2. Indicar la cantidad de entradas
3. Hacer clic en **"Comprar entradas"**
4. Revisar el resumen del pedido (subtotal + 5% de cargo de servicio)
5. Confirmar y ser redirigido al pago con **Mercado Pago**
6. Completar el pago en la plataforma de MP
7. Recibir confirmación y acceder a las entradas

#### Ver mis pedidos y entradas

- **Mis pedidos**: `/buyer/orders` — listado de todos los pedidos con su estado
- **Detalle de pedido**: `/buyer/orders/{id}` — ver entradas individuales de un pedido
- **Mis entradas**: `/buyer/tickets` — listado de todas las entradas
- **Ver entrada**: `/buyer/tickets/{id}` — código QR + datos de la entrada

#### Descargar entrada (QR)

1. Ir a **Mis entradas** → clic en una entrada específica
2. El código QR se muestra en pantalla
3. Se puede imprimir la página o guardar la imagen del QR

---

### 7.2 Organizador

> Para acceder al panel de organizador, el usuario debe tener el rol `organizer`. El administrador puede asignar este rol.

#### Acceder al panel

URL: **http://localhost:8000/organizer/dashboard**

El dashboard muestra:
- Total de eventos activos
- Total de entradas vendidas
- Ingresos totales

#### Crear un evento

1. Ir a **Organizer → Mis Eventos → Crear evento**
2. Completar el formulario:
   - **Título** del evento
   - **Descripción** detallada
   - **Categoría** (Música, Teatro, Deportes, etc.)
   - **Venue** (lugar del evento)
   - **Fecha y hora** de inicio y fin
   - **Estado inicial**: `draft` (borrador) o `published` (publicado)
3. Guardar el evento

> Los eventos en estado `draft` no son visibles para el público hasta ser publicados.

#### Gestionar tipos de entrada

1. En la lista de eventos, hacer clic en **"Tipos de entrada"** del evento deseado
2. Crear tipos con:
   - **Nombre**: General, VIP, Campo, etc.
   - **Precio** en ARS
   - **Cantidad disponible**
   - **Período de venta**: fecha/hora de inicio y fin (opcional)
3. Se pueden crear múltiples tipos por evento

#### Estadísticas del evento

Desde el panel del organizador se pueden ver:
- Entradas vendidas por tipo
- Ingresos generados
- Capacidad restante

#### Escanear entradas en puerta

1. Ir a **Organizer → Escanear Entrada**
2. Ingresar el código de la entrada (UUID impreso en la entrada o escaneado con lector QR)
3. El sistema indica:
   - ✅ **Válida** — entrada correcta, se marca como usada
   - ⚠️ **Ya utilizada** — la entrada fue escaneada anteriormente
   - ❌ **Inválida** — código no encontrado
   - 🚫 **Cancelada** — entrada anulada

---

### 7.3 Administrador

> URL base del panel: **http://localhost:8000/admin/dashboard**

#### Dashboard general

Muestra estadísticas globales de la plataforma:
- Total de usuarios registrados
- Total de organizadores
- Total de eventos
- Total de entradas vendidas

#### Gestión de usuarios

URL: `/admin/users`

- Ver todos los usuarios registrados con su rol actual
- Cambiar el rol de un usuario (buyer ↔ organizer ↔ admin) con un clic

#### Gestión de organizadores

URL: `/admin/organizers`

- Ver todos los perfiles de organizador
- **Verificar** un organizador (muestra insignia de verificado)
- **Revocar verificación** si es necesario

> Los organizadores verificados generan mayor confianza en los compradores.

#### Gestión de eventos

URL: `/admin/events`

- Ver todos los eventos de la plataforma con su estado
- **Publicar** un evento en estado borrador
- **Despublicar** / marcar como cancelado

---

## 8. Flujo completo de compra

```
Comprador                   Sistema                        Mercado Pago
    │                          │                               │
    │── Selecciona entradas ──►│                               │
    │                          │ Valida disponibilidad         │
    │◄── Resumen del pedido ───│                               │
    │                          │                               │
    │── Confirma compra ──────►│                               │
    │                          │ Crea Orden (status: pending)  │
    │                          │── Crea preferencia MP ───────►│
    │◄── Redirige a MP ────────│◄─ Devuelve preference_id ────│
    │                          │                               │
    │── Paga en MP ───────────────────────────────────────────►│
    │                          │◄──── Webhook IPN ────────────│
    │                          │ Aprueba Orden                 │
    │                          │ Genera entradas (UUID + QR)   │
    │◄── Redirige a /success ──│                               │
    │                          │                               │
    │── Ver mis entradas ─────►│                               │
    │◄── Entradas con QR ──────│                               │
```

### Estados de una orden

| Estado | Descripción |
|--------|-------------|
| `pending` | Orden creada, esperando pago |
| `approved` | Pago confirmado, entradas generadas |
| `rejected` | Pago rechazado por Mercado Pago |
| `cancelled` | Orden cancelada manualmente |

### Cálculo de precios

```
Subtotal  = Σ (precio_tipo × cantidad)
Cargo     = Subtotal × 5%   (configurable en config/tickets.php)
Total     = Subtotal + Cargo
```

---

## 9. Integración con Mercado Pago

### Configuración

1. Crear una cuenta en [mercadopago.com.ar](https://www.mercadopago.com.ar)
2. Ir a **Tu negocio → Configuración → Credenciales**
3. Copiar **Public Key** y **Access Token** (usar las de *prueba* para desarrollo)
4. Agregar al `.env`:

```dotenv
MP_PUBLIC_KEY=TEST-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MP_ACCESS_TOKEN=TEST-0000000000000000-000000-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-000000000
MP_WEBHOOK_SECRET=tu_secreto_webhook
```

### Credenciales de prueba (sandbox)

En el panel de MP, usar las **credenciales de prueba** para simular pagos sin dinero real. Los pagos de prueba se hacen con tarjetas de crédito ficticias que provee Mercado Pago.

### Webhook

Configurar en el panel de MP la URL de webhook:
```
https://tu-dominio.com/payment/webhook
```

El sistema procesa automáticamente las notificaciones y aprueba/rechaza las órdenes.

### Modo demo (desarrollo local)

Cuando `APP_ENV=local`, el sistema simula automáticamente la aprobación del pago sin necesitar credenciales reales de MP. Esto permite desarrollar y probar el flujo completo sin configurar Mercado Pago.

---

## 10. Escaneo de entradas (QR)

### Desde el navegador (organizador)

1. El portero o staff del evento accede a `/organizer/scan`
2. Escribe o pega el código de la entrada (está impreso debajo del QR)
3. El sistema responde instantáneamente con el resultado

### Con lector de código de barras / QR físico

Los lectores QR USB o Bluetooth funcionan como teclados. Al enfocar el campo de texto en `/organizer/scan` y escanear el código:
1. El lector llena automáticamente el campo
2. Presionar **Enter** o el botón **"Escanear"**
3. El sistema valida la entrada

### Con teléfono (cámara)

Cualquier aplicación de escaneo QR en el teléfono puede leer el QR de la entrada. El resultado del escaneo es el código UUID que se debe ingresar en el sistema.

### Códigos de resultado del escaneo

| Ícono | Estado | Descripción |
|-------|--------|-------------|
| ✅ | `valid` | Entrada válida, acceso permitido |
| ⚠️ | `already_used` | Entrada ya fue utilizada |
| ❌ | `invalid` | Código no existe en el sistema |
| 🚫 | `cancelled` | Entrada cancelada o expirada |

---

## 11. Estructura del proyecto

```
Recoticket/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Panel administrador
│   │   │   ├── Auth/               # Login y registro
│   │   │   ├── Buyer/              # Panel comprador
│   │   │   ├── Organizer/          # Panel organizador
│   │   │   ├── CheckoutController  # Proceso de compra
│   │   │   ├── EventController     # Vista pública de eventos
│   │   │   ├── HomeController      # Página principal
│   │   │   └── PaymentController   # Mercado Pago
│   │   └── Middleware/
│   │       └── CheckRole.php       # Control de acceso por rol
│   ├── Models/                     # Modelos Eloquent
│   │   ├── Category.php
│   │   ├── Event.php
│   │   ├── Order.php / OrderItem.php
│   │   ├── Organizer.php
│   │   ├── Payment.php
│   │   ├── Ticket.php / TicketScan.php
│   │   ├── TicketType.php
│   │   ├── User.php
│   │   └── Venue.php
│   └── Services/
│       ├── OrderService.php        # Lógica de creación y aprobación de órdenes
│       ├── TicketService.php       # Generación y validación de entradas
│       └── QrCodeService.php       # Generación de códigos QR (SVG)
├── config/
│   └── tickets.php                 # Porcentaje de cargo de servicio (5%)
├── database/
│   ├── migrations/                 # 12 migraciones de base de datos
│   └── seeders/
│       └── DatabaseSeeder.php      # Datos de prueba
├── resources/views/
│   ├── layouts/                    # Layouts base (app + guest)
│   ├── auth/                       # Login y registro
│   ├── admin/                      # Vistas del panel admin
│   ├── buyer/                      # Vistas del panel comprador
│   ├── organizer/                  # Vistas del panel organizador
│   ├── events/                     # Detalle y checkout de eventos
│   ├── payment/                    # Éxito y error de pago
│   └── home.blade.php              # Página principal
└── routes/
    └── web.php                     # Definición de todas las rutas
```

---

## 12. Referencia de rutas

### Rutas públicas (sin autenticación)

| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/` | Página principal con eventos publicados |
| GET | `/events/{slug}` | Detalle de un evento |
| GET | `/login` | Formulario de inicio de sesión |
| POST | `/login` | Procesar login |
| GET | `/register` | Formulario de registro |
| POST | `/register` | Procesar registro |
| POST | `/logout` | Cerrar sesión |

### Rutas de comprador (requiere autenticación)

| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/events/{slug}/checkout` | Formulario de checkout |
| POST | `/events/{slug}/checkout` | Procesar compra |
| GET | `/payment/checkout/{order}` | Iniciar pago con MP |
| GET | `/payment/success` | Confirmación de pago exitoso |
| GET | `/payment/failure` | Notificación de pago fallido |
| GET | `/buyer/dashboard` | Dashboard del comprador |
| GET | `/buyer/orders` | Lista de pedidos |
| GET | `/buyer/orders/{id}` | Detalle de un pedido |
| GET | `/buyer/tickets` | Lista de entradas |
| GET | `/buyer/tickets/{id}` | Detalle / QR de una entrada |

### Rutas de organizador (requiere rol `organizer`)

| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/organizer/dashboard` | Dashboard del organizador |
| GET | `/organizer/events` | Lista de eventos |
| GET | `/organizer/events/create` | Formulario nuevo evento |
| POST | `/organizer/events` | Crear evento |
| GET | `/organizer/events/{id}/edit` | Editar evento |
| PUT | `/organizer/events/{id}` | Actualizar evento |
| DELETE | `/organizer/events/{id}` | Eliminar evento |
| GET | `/organizer/events/{id}/ticket-types` | Tipos de entrada del evento |
| GET | `/organizer/ticket-types/create` | Crear tipo de entrada |
| POST | `/organizer/ticket-types` | Guardar tipo de entrada |
| GET | `/organizer/ticket-types/{id}/edit` | Editar tipo de entrada |
| PUT | `/organizer/ticket-types/{id}` | Actualizar tipo de entrada |
| DELETE | `/organizer/ticket-types/{id}` | Eliminar tipo de entrada |
| GET | `/organizer/scan` | Pantalla de escaneo QR |
| POST | `/organizer/scan` | Validar código de entrada |

### Rutas de administrador (requiere rol `admin`)

| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/admin/dashboard` | Dashboard global |
| GET | `/admin/users` | Lista de usuarios |
| POST | `/admin/users/{id}/role` | Cambiar rol de usuario |
| GET | `/admin/organizers` | Lista de organizadores |
| POST | `/admin/organizers/{id}/verify` | Verificar/desverificar organizador |
| GET | `/admin/events` | Lista de todos los eventos |
| POST | `/admin/events/{id}/publish` | Publicar o despublicar evento |

### Webhook de pagos

| Método | URL | Descripción |
|--------|-----|-------------|
| POST | `/payment/webhook` | Webhook IPN de Mercado Pago |

---

## Preguntas frecuentes

**¿Cómo cambio el porcentaje de cargo de servicio?**
Editar `config/tickets.php` y cambiar el valor de `fee_percentage` (por defecto `0.05` = 5%).

**¿Cómo agrego nuevas categorías?**
Desde cualquier cliente de base de datos o via Tinker:
```bash
php artisan tinker
>>> \App\Models\Category::create(['name' => 'Mi Categoría', 'slug' => 'mi-categoria', 'icon' => '🎯']);
```

**¿Qué pasa si Mercado Pago no está configurado?**
En `APP_ENV=local`, el sistema usa el modo demo y aprueba los pagos automáticamente. En producción, se mostrará un error si las credenciales no están configuradas.

**¿Cómo resetear la base de datos?**
```bash
php artisan migrate:fresh --seed
```

---

*RecoTicket — Hecho con ❤️ en Argentina*
