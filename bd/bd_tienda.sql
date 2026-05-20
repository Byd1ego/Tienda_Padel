-- =========================
-- BASE DE DATOS
-- =========================
CREATE DATABASE tienda_padel;
USE tienda_padel;

-- =========================
-- TABLA: producto
-- Guarda todas las palas de la tienda
-- cod_producto es la clave primaria, por ejemplo 'P001'
-- exclusiva indica si la pala solo se vende en esta tienda
-- =========================
CREATE TABLE producto (
    cod_producto VARCHAR(12)  PRIMARY KEY,
    nombre       VARCHAR(200) NOT NULL,
    nombre_corto VARCHAR(50),
    descripcion  TEXT,
    marca        VARCHAR(100),
    nivel        ENUM('principiante', 'intermedio', 'avanzado'),
    forma        ENUM('redonda', 'lagrima', 'diamante'),
    peso         INT,
    pvp          DECIMAL(10,2) NOT NULL,
    exclusiva    BOOLEAN DEFAULT FALSE,
    imagen       VARCHAR(255)
);

-- =========================
-- TABLA: tienda
-- Guarda los datos de la tienda física
-- De momento solo hay una tienda con cod_tienda = 1
-- =========================
CREATE TABLE tienda (
    cod_tienda INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    tlf        VARCHAR(13)
);

-- =========================
-- TABLA: stock
-- Relaciona producto con tienda y dice cuántas unidades hay
-- La clave primaria es la combinación de cod_producto + cod_tienda
-- Cuando alguien compra, se restan unidades aquí
-- =========================
CREATE TABLE stock (
    cod_producto VARCHAR(12),
    cod_tienda   INT,
    unidades     INT DEFAULT 0,

    PRIMARY KEY (cod_producto, cod_tienda),
    FOREIGN KEY (cod_producto) REFERENCES producto(cod_producto),
    FOREIGN KEY (cod_tienda)   REFERENCES tienda(cod_tienda)
);

-- =========================
-- TABLA: contacto
-- Guarda los mensajes del formulario de contacto
-- =========================
CREATE TABLE contacto (
    id_contacto INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    telefono    VARCHAR(9)   NOT NULL,
    mensaje     TEXT         NOT NULL,
    fecha       DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABLA: foro
-- Guarda los comentarios del foro
-- No hace falta usuario registrado, solo un nick
-- =========================
CREATE TABLE foro (
    id_foro    INT AUTO_INCREMENT PRIMARY KEY,
    nick       VARCHAR(100) NOT NULL,
    comentario TEXT         NOT NULL,
    fecha      DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABLA: carrito
-- Guarda los productos que el usuario ha añadido pero aún no ha pagado
-- Cuando paga, se borran de aquí y se copian a pedido
-- La foreign key con producto evita añadir palas que no existen
-- =========================
CREATE TABLE carrito (
    id_carrito   INT AUTO_INCREMENT PRIMARY KEY,
    usuario      VARCHAR(100) NOT NULL,
    cod_producto VARCHAR(12)  NOT NULL,
    unidades     INT DEFAULT 1,
    fecha        DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (cod_producto) REFERENCES producto(cod_producto)
);

-- =========================
-- TABLA: pedido
-- Histórico de compras realizadas
-- Guarda nombre e imagen directamente para que si se borra
-- el producto, el pedido siga teniendo sus datos
-- =========================
CREATE TABLE pedido (
    id_pedido       INT AUTO_INCREMENT PRIMARY KEY,
    usuario         VARCHAR(100)  NOT NULL,
    cod_producto    VARCHAR(12)   NOT NULL,
    nombre_producto VARCHAR(50)   NOT NULL,
    unidades        INT           NOT NULL,
    pvp             DECIMAL(10,2) NOT NULL,
    imagen          VARCHAR(255),
    fecha           DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (cod_producto) REFERENCES producto(cod_producto)
);