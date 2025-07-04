-- Create database
CREATE DATABASE IF NOT EXISTS ecommerce_orders;
USE ecommerce_orders;

-- Users table (if not exists)
CREATE TABLE IF NOT EXISTS users (
    u_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table (if not exists)
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_title VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    discounted_price DECIMAL(10,2),
    product_img VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table with admin commands
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaction_id VARCHAR(100),
    total_amount DECIMAL(10,2) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    house_number VARCHAR(100) NOT NULL,
    street_road VARCHAR(100) NOT NULL,
    town_city VARCHAR(100) NOT NULL,
    post_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email_address VARCHAR(100) NOT NULL,
    order_status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    admin_command VARCHAR(255),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(u_id)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- Insert sample users
INSERT INTO users (username, password, email) VALUES 
('admin', MD5('admin123'), 'admin@example.com'),
('testuser', MD5('password'), 'user@example.com');