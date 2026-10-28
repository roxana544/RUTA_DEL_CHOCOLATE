# Ruta del Chocolate — Sucre

Sitio web para que los turistas descubran las fábricas de chocolate de Sucre, reserven tours y compren chocolates para recoger en la fábrica o en sus tiendas.

- **Laravel 13** + MySQL (XAMPP) · Tailwind CSS 4 · Leaflet
- Portada con **mapa satelital real (Esri) guiado por el scroll**: vuela de Bolivia a Sucre, se inclina en 3D y un viajero recorre las calles de fábrica en fábrica mientras se dibuja la ruta.
- **Fábricas** (`/fabricas`): ubicación, horarios de visita, teléfono/web, museo, productos y precios.
- **Cuentas de turista y checkpoints** (`/registro`, `/mi-ruta`): el turista registra su paso en cada fábrica con **foto + GPS**, suma puntos y gana insignias; historial de reservas. Admin → *Checkpoints* muestra la afluencia real por fábrica.
- **Tours** con cupos por horario, días de salida y reservas con código (`RC-XXXXXX`). Pago al llegar.
- **Tienda** con carrito; el pedido (`CH-XXXXXX`) se recoge en la fábrica o tienda elegida y puede vincularse a una reserva de tour. Pago al recoger.
- **Español / Inglés** (selector ES/EN).
- **Panel admin** en `/admin`: reservas, pedidos, tours, productos, fábricas y puntos de recojo.

## Puesta en marcha (XAMPP)

1. Inicia **MySQL** y **Apache** desde XAMPP Manager.
2. Crea la base de datos:
   ```bash
   /Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "CREATE DATABASE IF NOT EXISTS ruta_chocolate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   ```
3. Crea las tablas y los datos iniciales:
   ```bash
   php artisan migrate --seed
   ```
4. Abre **http://localhost/ruta-del-chocolate/** (o ejecuta `php artisan serve` y abre http://127.0.0.1:8000).

### Acceso admin

- URL: `/admin`
- Usuario: `admin@rutadelchocolate.bo`
- Contraseña: `chocolate123` — **cámbiala** antes de publicar el sitio.

## Eventos, guías y recuerdos (Cap. III del documento)

Roles: **Turista**, **Guía / Agencia** (`/gestion`) y **Administrador** (hereda al guía, más `/admin`). Se asignan en *Admin → Usuarios*.

- **Turista** (`/registro`, `/panel`): ve eventos (`/eventos`), filtra por estado/tour, se inscribe con formulario (control de cupo), recibe correo de confirmación, participa en actividades por parada (checkpoint foto+GPS, foto libre, quiz) y recibe su **recuerdo** (`/recuerdos/{token}`) al finalizar.
- **Guía**: resumen de eventos, *Crear evento* (tour, fecha, hora, cupo, paradas con orden y hora), *Mis eventos*, editar, **Iniciar** (abre las actividades) y **Finalizar** (cierra actividades, genera recuerdos y los envía por correo). Gestiona actividades en *Actividades*.
- Las reservas de tour por fecha y horario (con código `RC-`) y la tienda siguen funcionando.
- Los correos usan `MAIL_MAILER=log` (quedan en `storage/logs/laravel.log`); configura SMTP en `.env` para enviarlos de verdad.

Cuentas de demostración (cambia las contraseñas antes de publicar): `admin@rutadelchocolate.bo` y `guia@rutadelchocolate.bo`, ambas con `chocolate123`.

## Checkpoints

- Por defecto exige GPS a menos de `CHECKPOINT_RADIUS_M` metros (300) de la fábrica: 10 puntos (+50 al completar la ruta).
- Para **pruebas locales** `.env` trae `CHECKPOINT_STRICT=false`: acepta la foto sin GPS (5 puntos, marcado "sin verificar"). Ponlo en `true` en producción.
- El GPS del navegador solo funciona en HTTPS o `localhost`.

## Desarrollo

```bash
npm install
npm run dev     # recarga en caliente mientras editas
npm run build   # compila CSS/JS para producción (public/build)
```

Si cambias CSS o JS y no usas `npm run dev`, ejecuta `npm run build` para ver los cambios.

## Dónde está cada cosa

| Qué | Archivo |
| --- | --- |
| Mapa con scroll (portada) | `resources/js/scroll-map.js`, `resources/views/home.blade.php` |
| Mapas de tour y de recojo | `resources/js/maps.js` |
| Estilos / paleta cacao | `resources/css/app.css` |
| Textos en inglés | `lang/en.json` (las claves son el texto en español) |
| Datos iniciales | `database/seeders/DatabaseSeeder.php` |
| Rutas | `routes/web.php` |

## Notas

- Las coordenadas de las fábricas son **aproximadas**; corrígelas en *Admin → Fábricas* (clic derecho en Google Maps para copiar latitud/longitud).
- Los mapas usan imágenes satelitales gratuitas de Esri (sin API key; detalle real hasta zoom 17) y las calles de la ruta vienen del router público OSRM (si no responde, se dibuja línea recta).
- Las 5 fábricas del documento (Para Ti, Taboada, Chocolates Sucre, El Ceibo, Mi Bombón) tienen coordenadas y horarios **aproximados**: corrígelos en *Admin → Fábricas*.
