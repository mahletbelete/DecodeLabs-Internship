
CREATE DATABASE IF NOT EXISTS morningmug
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE morningmug;

CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)        NOT NULL,
    email      VARCHAR(150)        NOT NULL UNIQUE,
    password   VARCHAR(255)        NOT NULL,  
    role       ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)   NOT NULL,
    description TEXT,
    price       DECIMAL(8, 2)  NOT NULL,
    category    VARCHAR(80)    NOT NULL,
    image       VARCHAR(255)   DEFAULT NULL,  
    available   TINYINT(1)     NOT NULL DEFAULT 1,
    created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED   DEFAULT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status      ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id    INT UNSIGNED   NOT NULL,
    product_id  INT UNSIGNED   NOT NULL,
    quantity    INT UNSIGNED   NOT NULL,
    unit_price  DECIMAL(8, 2)  NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    message    TEXT         NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@morningmug.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');


INSERT INTO products (name, description, price, category, image, available) VALUES
('Espresso',          'A short, strong shot of pure coffee.',                          3.50, 'Coffee',      'Espresso.png',         1),
('Cappuccino',        'Espresso topped with steamed milk and thick foam.',             4.50, 'Coffee',      'Cappuccino.png',       1),
('Latte',             'Smooth espresso blended with silky steamed milk.',              4.75, 'Coffee',      'Latte.png',            1),
('Flat White',        'Velvety microfoam over a double ristretto.',                    4.50, 'Coffee',      'Flatwhite.png',        1),
('Americano',         'Espresso diluted with hot water for a mellow cup.',             3.75, 'Coffee',      'Americano.png',        1),
('Cold Brew',         'Slow-steeped for 12 hours — smooth and refreshing.',            5.00, 'Cold Drinks', 'ColdBrew.png',         1),
('Iced Latte',        'Chilled espresso over ice with cold milk.',                     5.25, 'Cold Drinks', 'IcedLatte.png',        1),
('Matcha Latte',      'Premium ceremonial matcha with oat milk.',                      5.50, 'Specialty',   'MatchaLatte.png',      1),
('Chai Latte',        'Spiced black tea with steamed milk and honey.',                 4.75, 'Specialty',   'ChaiLatte.png',        1),
('Macchiato',         'Bold espresso marked with a dollop of foamed milk.',            4.25, 'Coffee',      'Macchiato.png',        1),
('Iced Americano',    'Espresso shots poured over ice for a crisp, refreshing drink.', 4.50, 'Cold Drinks', 'IcedAmericano.png',    1),
('Caramel Macchiato', 'Vanilla-infused milk topped with espresso and caramel drizzle.',5.25, 'Specialty',   'CaramelMacchiato.png', 1);

