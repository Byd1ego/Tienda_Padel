-- =========================
-- BASE DE DATOS
-- =========================
CREATE DATABASE tienda_padel;
USE tienda_padel;

-- =========================
-- TABLA: producto (solo palas)
-- =========================
CREATE TABLE producto (
    cod VARCHAR(12) PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    nombre_corto VARCHAR(50),
    descripcion TEXT,
    marca VARCHAR(100),
    nivel ENUM('principiante', 'intermedio', 'avanzado'),
    forma ENUM('redonda', 'lagrima', 'diamante'),
    peso INT,
    pvp DECIMAL(10,2) NOT NULL,
    exclusiva BOOLEAN DEFAULT FALSE,
    imagen VARCHAR(255)
);

-- =========================
-- TABLA: tienda
-- =========================
CREATE TABLE tienda (
    cod INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tlf VARCHAR(13)
);

-- =========================
-- TABLA: stock
-- =========================
CREATE TABLE stock (
    producto VARCHAR(12),
    tienda INT,
    unidades INT DEFAULT 0,

    PRIMARY KEY (producto, tienda),
    FOREIGN KEY (producto) REFERENCES producto(cod),
    FOREIGN KEY (tienda) REFERENCES tienda(cod)
);

-- =========================
-- TABLA: contacto
-- =========================
CREATE TABLE contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefono VARCHAR(9) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABLA: foro
-- =========================
CREATE TABLE foro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nick VARCHAR(100) NOT NULL,
    comentario TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);
