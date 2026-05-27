# 🏓 PadelZone — Tienda Online de Pádel
 
> **Trabajo de Fin de Grado · Desarrollo de Aplicaciones Web (DAW)**  
> Aplicación web de comercio electrónico desarrollada con PHP, MySQL y JavaScript
 
---
 
## Índice
 
1. [Descripción del proyecto](#descripción-del-proyecto)
2. [Tecnologías utilizadas](#tecnologías-utilizadas)
3. [Estructura del proyecto](#estructura-del-proyecto)
4. [Base de datos](#base-de-datos)
5. [Funcionalidades](#funcionalidades)
6. [Roles y acceso](#roles-y-acceso)
7. [Páginas y rutas](#páginas-y-rutas)
8. [Panel de administración](#panel-de-administración)
9. [Librerías externas](#librerías-externas)
---
 
## Descripción del proyecto
 
**PadelZone** es una tienda online especializada en palas de pádel. Permite a los usuarios explorar el catálogo de productos, añadir artículos al carrito y completar compras. Además, incluye un foro de opiniones, una sección de productos exclusivos para usuarios registrados y un panel de administración para gestionar el inventario.
 
---
 
## Tecnologías utilizadas
 
| Capa | Tecnología |
|---|---|
| Backend | PHP 8 |
| Base de datos | MySQL / MariaDB |
| Frontend | HTML5, CSS3, JavaScript |
| Librería JS | jQuery 3.7.1 |
| Generación PDF | FPDF (PHP) |
| Iconos | Font Awesome 6.5 |
| API externa | Open-Meteo (temperatura en tiempo real) |
 
---
 
## Estructura del proyecto
 
```
Tienda_Padel/
│
├── index.php                  # Página principal — catálogo con paginación
├── producto.php               # Ficha detallada de un producto
├── carrito.php                # Carrito de compra del usuario
├── añadir_carrito.php         # Lógica para añadir productos al carrito
├── exclusivas.php             # Sección de productos exclusivos (requiere login)
├── foro.php                   # Foro de comentarios (requiere login)
├── contacto.php               # Formulario de contacto
├── login.php                  # Inicio de sesión
├── logout.php                 # Cierre de sesión
│
├── admin/
│   ├── productos.php          # Listado de productos (panel admin)
│   ├── producto_nuevo.php     # Formulario para crear un producto
│   └── producto_editar.php    # Formulario para editar un producto
│
├── static/
│   ├── conexion.php           # Conexión PDO a la base de datos
│   ├── configuracion.php      # Constantes de conexión (host, user, pass, dbname)
│   ├── header.php             # Cabecera HTML + inicio de sesión
│   ├── header_admin.php       # Cabecera del panel de administración
│   ├── footer.php             # Pie de página público
│   ├── footer_admin.php       # Pie de página del panel admin
│   ├── funciones.php          # Funciones auxiliares
│   └── fpdf/                  # Librería FPDF para generación de PDFs
│
├── includes/
│   ├── css/
│   │   └── estilos.css        # Hoja de estilos principal
│   └── img/                   # Imágenes de productos y banners
│
└── bd/
    ├── bd_tienda.sql          # Script de creación de tablas
    └── inserts.sql            # Datos iniciales (productos, stock, tienda)
```
 
---
 
## Base de datos
 
La base de datos se llama `tienda_padel` y está compuesta por las siguientes tablas:
 
### `producto`
Almacena el catálogo completo de palas.
 
| Campo | Tipo | Descripción |
|---|---|---|
| `cod_producto` | VARCHAR(12) PK | Código único, p. ej. `P001` |
| `nombre` | VARCHAR(200) | Nombre completo |
| `nombre_corto` | VARCHAR(50) | Nombre corto para tarjetas |
| `descripcion` | TEXT | Descripción detallada |
| `marca` | VARCHAR(100) | Marca de la pala |
| `nivel` | ENUM | `principiante`, `intermedio`, `avanzado` |
| `forma` | ENUM | `redonda`, `lagrima`, `diamante` |
| `peso` | INT | Peso en gramos |
| `pvp` | DECIMAL(10,2) | Precio de venta al público |
| `exclusiva` | BOOLEAN | `TRUE` si es producto exclusivo |
| `imagen` | VARCHAR(255) | Nombre del archivo de imagen |
 
### `tienda`
Datos de la tienda física (actualmente solo hay una, con `cod_tienda = 1`).
 
| Campo | Tipo | Descripción |
|---|---|---|
| `cod_tienda` | INT PK AUTO | Identificador |
| `nombre` | VARCHAR(100) | Nombre de la tienda |
| `tlf` | VARCHAR(13) | Teléfono de contacto |
 
### `stock`
Relación entre productos y tienda con las unidades disponibles.
 
| Campo | Tipo | Descripción |
|---|---|---|
| `cod_producto` | VARCHAR(12) FK | Referencia a `producto` |
| `cod_tienda` | INT FK | Referencia a `tienda` |
| `unidades` | INT | Unidades en stock |
 
La clave primaria es la combinación `(cod_producto, cod_tienda)`.
 
### `carrito`
Productos añadidos por un usuario que aún no ha completado la compra.
 
| Campo | Tipo | Descripción |
|---|---|---|
| `id_carrito` | INT PK AUTO | Identificador |
| `usuario` | VARCHAR(100) | Nombre del usuario |
| `cod_producto` | VARCHAR(12) FK | Referencia a `producto` |
| `unidades` | INT | Cantidad deseada |
| `fecha` | DATETIME | Fecha de adición |
 
### `pedido`
Histórico de compras realizadas. Guarda `nombre_producto` e `imagen` directamente para preservar el registro aunque el producto sea eliminado.
 
| Campo | Tipo | Descripción |
|---|---|---|
| `id_pedido` | INT PK AUTO | Identificador |
| `usuario` | VARCHAR(100) | Nombre del usuario |
| `cod_producto` | VARCHAR(12) FK | Referencia a `producto` |
| `nombre_producto` | VARCHAR(50) | Nombre en el momento de la compra |
| `unidades` | INT | Unidades compradas |
| `pvp` | DECIMAL(10,2) | Precio en el momento de la compra |
| `imagen` | VARCHAR(255) | Imagen en el momento de la compra |
| `fecha` | DATETIME | Fecha del pedido |
 
### `contacto`
Mensajes recibidos a través del formulario de contacto.
 
| Campo | Tipo | Descripción |
|---|---|---|
| `id_contacto` | INT PK AUTO | Identificador |
| `nombre` | VARCHAR(100) | Nombre del remitente |
| `email` | VARCHAR(150) | Email del remitente |
| `telefono` | VARCHAR(9) | Teléfono del remitente |
| `mensaje` | TEXT | Contenido del mensaje |
| `fecha` | DATETIME | Fecha de recepción |
 
### `foro`
Comentarios publicados en el foro. No requiere usuario registrado, solo un nick.
 
| Campo | Tipo | Descripción |
|---|---|---|
| `id_foro` | INT PK AUTO | Identificador |
| `nick` | VARCHAR(100) | Nombre elegido por el usuario |
| `comentario` | TEXT | Contenido del comentario |
| `fecha` | DATETIME | Fecha de publicación |
 
### Diagrama de relaciones (simplificado)
 
```
producto ──< stock >── tienda
producto ──< carrito
producto ──< pedido
```
 
---
 
## Funcionalidades
 
### Catálogo con paginación
La página principal muestra los productos no exclusivos con soporte de paginación. El usuario puede elegir cuántos productos ver por página (6, 12 o 24) y su preferencia se guarda en una **cookie** de 30 días.
 
### Ficha de producto
Cada producto tiene una página de detalle con imagen, descripción, especificaciones técnicas (marca, nivel, forma, peso) y el stock disponible. Desde aquí el usuario registrado puede añadir el artículo al carrito.
 
### Carrito de compra
Disponible solo para usuarios con rol `usuario`. Permite ver los productos añadidos, eliminar artículos individualmente y finalizar la compra. Al pagar se comprueba el stock disponible antes de procesar la transacción; si hay suficiente stock se descuenta de la tabla `stock`, se registra en `pedido` y el carrito queda vacío.
 
### Sección de exclusivas
Muestra los productos marcados como `exclusiva = TRUE`. Solo es accesible para usuarios con sesión iniciada.
 
### Foro de opiniones
Los usuarios registrados pueden publicar comentarios con un nick libre. Los comentarios se muestran ordenados del más reciente al más antiguo. Al entrar aparece un mensaje de bienvenida animado con efecto jQuery `slideDown`.
 
### Formulario de contacto
Disponible para cualquier visitante. Valida el teléfono con JavaScript (exactamente 9 dígitos) antes de enviar el formulario. Los mensajes se guardan en la tabla `contacto`.
 
### Temperatura en tiempo real
En la página de contacto se muestra la temperatura actual de Crevillente obtenida mediante una llamada AJAX a la API gratuita [Open-Meteo](https://open-meteo.com/).
 
### Slideshow de banners
La página principal incluye un carrusel de imágenes (banner) implementado con jQuery que cambia de imagen cada 3 segundos con efecto `fadeIn` / `fadeOut`.
 
---
 
## Roles y acceso
 
La autenticación está gestionada con **sesiones PHP** (`$_SESSION`). Existen dos roles con credenciales fijas:
 
| Rol | Usuario | Contraseña | Acceso |
|---|---|---|---|
| Administrador | `admin` | `1234` | Panel de administración (`/admin/`) |
| Usuario | `usuario` | `5678` | Carrito, exclusivas, foro |
 
> **Nota para el evaluador:** las credenciales están hardcodeadas en `login.php` a efectos de demostración del TFG.
 
### Comportamiento según el rol
 
- **Sin sesión:** acceso al catálogo, contacto y vista de productos. El menú no muestra Exclusivas ni el carrito.
- **Rol `usuario`:** acceso al carrito, exclusivas y foro. Se muestra el icono del carrito en el menú.
- **Rol `admin`:** acceso al panel de administración. El menú muestra el enlace "Administrar productos".
Las páginas protegidas redirigen a `login.php?redirigido=true` si el usuario intenta acceder sin sesión.
 
---
 
## Páginas y rutas
 
| Ruta | Descripción | Acceso |
|---|---|---|
| `index.php` | Catálogo principal con paginación | Público |
| `producto.php?cod=XXX` | Ficha de producto | Público |
| `carrito.php` | Carrito de compra | Rol `usuario` |
| `añadir_carrito.php` | Acción de añadir al carrito | Rol `usuario` |
| `exclusivas.php` | Productos exclusivos | Autenticado |
| `foro.php` | Foro de comentarios | Autenticado |
| `contacto.php` | Formulario de contacto | Público |
| `login.php` | Inicio de sesión | Público |
| `logout.php` | Cierre de sesión | Autenticado |
| `admin/productos.php` | Listado del panel admin | Rol `admin` |
| `admin/producto_nuevo.php` | Crear nuevo producto | Rol `admin` |
| `admin/producto_editar.php?cod=XXX` | Editar producto | Rol `admin` |
 
---
 
## Panel de administración
 
Accesible únicamente para el rol `admin`. Incluye:
 
- **Listado de productos** con acceso rápido a editar o eliminar.
- **Crear producto** (`producto_nuevo.php`): formulario completo con todos los campos de la tabla `producto` más el stock inicial y la subida de imagen.
- **Editar producto** (`producto_editar.php`): precarga los datos actuales del producto y permite modificarlos, incluyendo la imagen (si no se sube una nueva, se conserva la anterior) y el stock. Si el stock ya existe se hace un `UPDATE`; si no, se hace un `INSERT`.
El panel tiene su propia cabecera y pie de página (`header_admin.php` / `footer_admin.php`) separados del frontend público.
 
---
 
## Librerías externas
 
| Librería | Versión | Uso |
|---|---|---|
| [jQuery](https://jquery.com/) | 3.7.1 | Slideshow, efectos, llamadas AJAX |
| [Font Awesome](https://fontawesome.com/) | 6.5.0 | Iconos (carrito, menú hamburguesa) |
| [FPDF](http://www.fpdf.org/) | — | Generación de documentos PDF |
| [Open-Meteo API](https://open-meteo.com/) | — | Temperatura en tiempo real |
 
---
 
## Autor
 
Proyecto desarrollado por **Javier Diego Castello** como **Trabajo de Fin de Grado** del ciclo de **Desarrollo de Aplicaciones Web (DAW)**.

---
