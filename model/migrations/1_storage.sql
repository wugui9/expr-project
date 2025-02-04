CREATE TABLE IF NOT EXISTS `storage` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `address` TEXT NOT NULL,
    `phone` VARCHAR(15) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `capacity` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample data
INSERT INTO storage (city, postal_code, detailed_address, latitude, longitude, capacity_volume_of_the_warehouse, capacity_weight_of_the_warehouse) VALUES
    ('Paris', '75001', '123 Rue de Rivoli', 48.86222, 2.33889, 1000, 5000),
    ('Lyon', '69001', '45 Rue de la République', 45.76722, 4.83278, 800, 4000),
    ('Marseille', '13001', '78 La Canebière', 43.29694, 5.37639, 1200, 6000),
    ('Bordeaux', '33000', '12 Rue Sainte-Catherine', 44.84111, 0.57944, 600, 3000);
