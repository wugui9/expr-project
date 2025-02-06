CREATE TABLE IF NOT EXISTS user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(32) NOT NULL,
    role ENUM('ADMIN', 'CUSTOMER', 'DELIVERY_DRIVER') NOT NULL DEFAULT 'CUSTOMER'
); 

-- Insert test users (password for all users is 'password123')
INSERT INTO user (last_name, first_name, phone, email, password, salt, role) VALUES
-- Admin user
('Admin', 'Super', '0123456789', 'admin@express.com', 
'94919a94972425c7c4cc42da99ebcfdb1469e0d213a27123bfb3ec525d189b34', -- sha256('admin123' + 'admin_salt')
'admin_salt', 'ADMIN'),

-- Customer user
('Smith', 'John', '0123456788', 'customer@express.com',
'd0166009f3162bfeb9d9fccd26f35e0bce7043baee06d5bb069ffc15da3c8f80', -- sha256('password' + 'customer_salt')
'customer_salt', 'CUSTOMER'),

-- Delivery driver user
('Driver', 'Express', '0123456787', 'driver@express.com',
'a1e2600bbd1ffdfc21abd9fbeab82819862ecf31b176ef67f73bbd5460c22dc8', -- sha256('password' + 'driver_salt')
'driver_salt', 'DELIVERY_DRIVER'); 