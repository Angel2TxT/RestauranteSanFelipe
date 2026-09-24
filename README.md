# Restaurante San Felipe

Aplicación Laravel para pedidos de restaurante (tienda, carrito, órdenes y panel admin).

## Requisitos

- PHP 8.2+
- Composer
- MySQL 8 (Laragon recomendado)
- Node.js (opcional, solo si rebuild de Vite)

## Instalación rápida (con dump SQL)

1. Clona el proyecto y entra a la carpeta.
2. Copia el entorno e instala dependencias:

```bash
copy .env.example .env
composer install
php artisan key:generate
```

3. Configura MySQL en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurantSF
DB_USERNAME=root
DB_PASSWORD=
```

4. Importa la base de datos completa (estructura + datos de demo):

```bash
mysql -u root < database/dumps/restaurantSF.sql
```

En Laragon / Windows también puedes importar `database/dumps/restaurantSF.sql` desde HeidiSQL o phpMyAdmin.

5. Arranca el servidor:

```bash
php artisan serve
```

Abre: http://127.0.0.1:8000

## Alternativa sin dump (migraciones + seed)

```bash
php artisan migrate --seed
```

## Usuarios de demostración

Contraseña de todos: `password`

| Rol | Email |
|-----|--------|
| Admin | admin@sanfelipe.test |
| Cliente | cliente@sanfelipe.test |
| Empleado | empleado@sanfelipe.test |
| Repartidor | repartidor@sanfelipe.test |

- Tienda: http://127.0.0.1:8000/login
- Admin: http://127.0.0.1:8000/admin/home

## Notas

- La tienda (`/` y `/shop`) es pública; checkout y "mis órdenes" requieren login.
- Las imágenes de productos/categorías/sliders están en `public/images/`.
- El dump SQL está en `database/dumps/restaurantSF.sql`.
- El correo de confirmación usa Mailgun solo si configuras `MAILGUN_SECRET` en `.env` (si no, la orden se crea igual).
