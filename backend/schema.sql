CREATE DATABASE IF NOT EXISTS ecopark
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ecopark;

CREATE TABLE IF NOT EXISTS stations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_name VARCHAR(150) NOT NULL,
    power_kw INT NOT NULL,
    is_available TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS charge_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    station_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    kwh_requested INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (station_id)
        REFERENCES stations(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO stations (location_name, power_kw, is_available)
SELECT 'Station Centre-Ville', 50, 1
WHERE NOT EXISTS (
    SELECT 1 FROM stations WHERE location_name = 'Station Centre-Ville'
);

INSERT INTO stations (location_name, power_kw, is_available)
SELECT 'Station Gare Centrale', 150, 1
WHERE NOT EXISTS (
    SELECT 1 FROM stations WHERE location_name = 'Station Gare Centrale'
);

INSERT INTO stations (location_name, power_kw, is_available)
SELECT 'Station Eco-Quartier', 22, 0
WHERE NOT EXISTS (
    SELECT 1 FROM stations WHERE location_name = 'Station Eco-Quartier'
);

INSERT INTO stations (location_name, power_kw, is_available)
SELECT 'Station Parking Relais', 11, 1
WHERE NOT EXISTS (
    SELECT 1 FROM stations WHERE location_name = 'Station Parking Relais'
);
