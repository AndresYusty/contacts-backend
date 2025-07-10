-- Script para crear la base de datos de contactos
-- Ejecutar este script en MySQL antes de usar la aplicación

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS contacts_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE contacts_db;

-- Crear tabla de contactos
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de teléfonos
CREATE TABLE IF NOT EXISTS phones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunos datos de ejemplo
INSERT INTO contacts (first_name, last_name, email) VALUES
('Juan', 'Pérez', 'juan.perez@example.com'),
('María', 'García', 'maria.garcia@example.com'),
('Carlos', 'López', 'carlos.lopez@example.com');

INSERT INTO phones (contact_id, phone_number) VALUES
(1, '+1234567890'),
(1, '+0987654321'),
(2, '+5551234567'),
(3, '+1112223333'); 