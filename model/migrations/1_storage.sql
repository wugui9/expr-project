CREATE TABLE IF NOT EXISTS storage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    detailed_address TEXT NOT NULL,
    capacity_volume_of_the_warehouse INT NOT NULL COMMENT 'Storage capacity in cubic meters',
    capacity_weight_of_the_warehouse INT NOT NULL COMMENT 'Weight capacity in kilograms',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample data
INSERT INTO storage (city, postal_code, detailed_address, capacity_volume_of_the_warehouse, capacity_weight_of_the_warehouse) VALUES
    ('Paris', '75001', '123 Rue de Rivoli', 1000, 5000),
    ('Lyon', '69001', '45 Rue de la République', 800, 4000),
    ('Marseille', '13001', '78 La Canebière', 1200, 6000),
    ('Bordeaux', '33000', '12 Rue Sainte-Catherine', 600, 3000);
