CREATE DATABASE bakerydb;
USE bakerydb;

CREATE TABLE cart (
    cart_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE customerinfo (
    customer_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNIQUE,
    phone VARCHAR(20),
    birthday DATE,
    address VARCHAR(255),
    payment VARCHAR(255),
    profpic VARCHAR(255),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE inquiry (
    ID INT(255) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Fname VARCHAR(25) NOT NULL,
    Lname VARCHAR(25) NOT NULL,
    email TEXT NOT NULL,
    Pnum VARCHAR(11) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    msg TEXT NOT NULL,
    dateSubmitted DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE login (
    user_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    Fname VARCHAR(255),
    Lname VARCHAR(255),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    role ENUM('admin','staff','customer') NOT NULL DEFAULT 'customer',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active'
);

CREATE TABLE notifications (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    is_read TINYINT(1) DEFAULT 0
);

CREATE TABLE order_items (
    order_item_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

CREATE TABLE orders (
    order_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    email VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    delivery_address TEXT NOT NULL,
    delivery_method ENUM('standard','pickup') NOT NULL,
    delivery_date DATE NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    delivery_fee DECIMAL(10,2) DEFAULT 0.00,
    payment_status ENUM('Pending','Paid','Failed','Refunded') NOT NULL DEFAULT 'Pending'
);

CREATE TABLE products (
    product_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255),
    availability ENUM('In Stock','Low Stock','Out of Stock') NOT NULL DEFAULT 'In Stock',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
);



DROP TABLE IF EXISTS custom_orders;
CREATE TABLE custom_orders (
    custom_order_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    order_id INT(11),
    cake_type ENUM('wedding', 'birthday', 'anniversary', 'custom') NOT NULL,
    cake_tiers INT(1) NOT NULL,
    cake_size ENUM('6', '8', '10', '12') NOT NULL,
    cake_flavor ENUM('vanilla', 'chocolate', 'redvelvet', 'carrot', 'ube') NOT NULL,
    filling_type ENUM('buttercream', 'ganache', 'fruit', 'custard') NOT NULL,
    frosting_type ENUM('buttercream', 'fondant', 'whipped', 'ganache') NOT NULL,
    special_instructions TEXT,
    reference_image LONGTEXT,  -- Changed to LONGTEXT to store base64 image data
    status ENUM('Pending Review', 'Approved', 'In Progress', 'Ready for Pickup', 'Completed', 'Cancelled') DEFAULT 'Pending Review',
    base_price DECIMAL(10, 2) NOT NULL,
    additional_charges DECIMAL(10, 2) DEFAULT 0.00,
    total_price DECIMAL(10, 2) GENERATED ALWAYS AS (base_price + additional_charges) STORED,
    estimated_completion_date DATE,
    delivery_address TEXT,
    delivery_date DATE,
    delivery_method ENUM('standard', 'pickup') NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',
    delivery_status ENUM('Pending', 'Processing', 'Shipped', 'Delivered') DEFAULT 'Pending',
    admin_comments TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    PRIMARY KEY (custom_order_id),
    FOREIGN KEY (user_id) REFERENCES login(user_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

INSERT INTO products (product_id, name, price, category, image, availability)
VALUES
    (1, 'Wedding Cake', 600.00, 'Wedding Cakes', '1.png', 'In Stock'),
    (2, 'Number Cake', 300.00, 'Birthday Cakes', '17.png', 'In Stock'),
    (3, 'Dog Cake', 1000.00, 'Birthday Cakes', '8.png', 'In Stock'),
    (4, 'Vanilla Cake', 1200.00, 'Shower Cakes', '2.png', 'In Stock'),
    (5, 'Cup Cakes', 100.00, 'Cupcakes', '3.png', 'In Stock'),
    (6, 'Fathers Day', 200.00, 'Celebration', '4.png', 'In Stock'),
    (7, 'Strawberry Cupcakes', 1200.00, 'Cupcakes', '5.png', 'In Stock'),
    (8, 'Ube Cupcakes', 500.00, 'Cupcakes', '6.png', 'In Stock'),
    (9, 'Jack Daniels Celebration', 100.00, 'Celebration', '10.png', 'In Stock'),
    (10, 'Monthsary Cake', 150.00, 'Wedding Cakes', '12.png', 'In Stock'),
    (11, 'Kiddie Cupcakes', 100.00, 'Birthday Cakes', '13.png', 'In Stock'),
    (12, 'Monthsary Cakes', 100.00, 'Wedding Cakes', '14.png', 'In Stock'),
    (13, 'Birthday Cakes', 100.00, 'Birthday Cakes', '15.png', 'In Stock'),
    (14, 'Coquette', 100.00, 'Breads', '16.png', 'In Stock'),
    (15, 'Tasty', 100.00, 'Breads', 'breads.png', 'In Stock');

    INSERT INTO login (user_id, email, password, Fname, Lname, role, status)
VALUES (
    1,
    'admin.bakery@gmail.com',
    'adminuser123',
    'Admin',
    'User',
    'admin',
    'active'
);