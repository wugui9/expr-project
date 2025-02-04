CREATE TABLE IF NOT EXISTS storage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    city VARCHAR(255) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    detailed_address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL DEFAULT 0,
    longitude DECIMAL(11, 8) NOT NULL DEFAULT 0,
    capacity_volume_of_the_warehouse INT NOT NULL COMMENT 'Storage capacity in cubic meters',
    capacity_weight_of_the_warehouse INT NOT NULL COMMENT 'Weight capacity in kilograms',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample data
INSERT INTO storage (city, postal_code, detailed_address, latitude, longitude, capacity_volume_of_the_warehouse, capacity_weight_of_the_warehouse) VALUES
    ('Paris', '75001', '123 Rue de Rivoli', 48.86222, 2.33889, 1000, 5000),
    ('Lyon', '69001', '45 Rue de la République', 45.76722, 4.83278, 800, 4000),
    ('Marseille', '13001', '78 La Canebière', 43.29694, 5.37639, 1200, 6000),
    ('Bordeaux', '33000', '12 Rue Sainte-Catherine', 44.84111, 0.57944, 600, 3000);
