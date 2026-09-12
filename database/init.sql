-- Database Schema for Sienna Retail Management System

CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description   TEXT         DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    product_id     INT AUTO_INCREMENT PRIMARY KEY,
    category_id    INT            NOT NULL,
    product_name   VARCHAR(150)   NOT NULL,
    cost_price     DECIMAL(10,2)  NOT NULL,
    selling_price  DECIMAL(10,2)  NOT NULL,
    stock_quantity INT            NOT NULL DEFAULT 0,
    reorder_level  INT            NOT NULL DEFAULT 10,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sales (
    transaction_id     INT AUTO_INCREMENT PRIMARY KEY,
    transaction_number VARCHAR(50)   NOT NULL UNIQUE,
    transaction_date   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    total_amount       DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transaction_details (
    detail_id      INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT            NOT NULL,
    product_id     INT            NOT NULL,
    quantity       INT            NOT NULL CHECK (quantity > 0),
    unit_price     DECIMAL(10,2)  NOT NULL,
    subtotal       DECIMAL(10,2)  NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES sales(transaction_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id)     REFERENCES products(product_id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed initial store inventory
INSERT INTO categories (category_name, description) VALUES
('Artisanal Goods', 'Handcrafted pottery, clay items, and home goods'),
('Pantry & Coffee', 'Specialty coffee beans, organic teas, and preserves'),
('Paper & Supplies', 'Handmade notebooks, linen paper, and desk tools');

INSERT INTO products (category_id, product_name, cost_price, selling_price, stock_quantity, reorder_level) VALUES
(1, 'Terracotta Ceramic Mug', 4.50, 16.00, 25, 5),
(1, 'Hand-poured Soy Candle', 3.00, 12.50, 4, 10),
(2, 'Roasted Siena Beans (250g)', 5.00, 14.00, 18, 8),
(2, 'Organic Chamomile Tea', 2.50, 8.50, 3, 5),
(3, 'Linen Hardcover Journal', 3.50, 11.00, 30, 10);

-- Seed a ready-to-use login (username: admin / password: admin123)
INSERT INTO users (username, email, password_hash) VALUES
('admin', 'admin@example.com', '$2y$10$aPjYX/iJYhQvRIXkhlM4Ue7Uy8DhHA4zOwgmmKk6Fm2fypl9493IC');

-- Seed a few past transactions so the Analytics/Dashboard page has real numbers
-- (product_id 1=Mug, 2=Candle, 3=Coffee Beans, 4=Chamomile Tea, 5=Journal)
INSERT INTO sales (transaction_number, transaction_date, total_amount) VALUES
('SR-DEMO0001', '2026-09-01 10:15:00', 46.00),
('SR-DEMO0002', '2026-09-04 14:32:00', 33.00),
('SR-DEMO0003', '2026-09-07 09:50:00', 45.50),
('SR-DEMO0004', '2026-09-10 16:05:00', 70.00);

INSERT INTO transaction_details (transaction_id, product_id, quantity, unit_price, subtotal) VALUES
-- SR-DEMO0001: 2 Mugs + 1 Coffee Beans
(1, 1, 2, 16.00, 32.00),
(1, 3, 1, 14.00, 14.00),
-- SR-DEMO0002: 3 Journals
(2, 5, 3, 11.00, 33.00),
-- SR-DEMO0003: 1 Candle + 2 Chamomile Tea + 1 Mug
(3, 2, 1, 12.50, 12.50),
(3, 4, 2, 8.50, 17.00),
(3, 1, 1, 16.00, 16.00),
-- SR-DEMO0004: 5 Coffee Beans (top revenue/fast-mover demo)
(4, 3, 5, 14.00, 70.00);