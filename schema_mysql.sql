CREATE TABLE IF NOT EXISTS company (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(128) NOT NULL,
    logo VARCHAR(256),
    id_number VARCHAR(13),
    tax_number VARCHAR(13),
    iban VARCHAR(34),
    address1 VARCHAR(80),
    address2 VARCHAR(80),
    zip VARCHAR(5),
    city VARCHAR(60),
    country VARCHAR(60),
    email VARCHAR(60),
    web VARCHAR(60)
);


CREATE TABLE IF NOT EXISTS product_category (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(80) NOT NULL,
    description TEXT,
    vat FLOAT NOT NULL DEFAULT 0.0,
    consumption_tax FLOAT NOT NULL DEFAULT 0.0,
    sales_tax FLOAT NOT NULL DEFAULT 0.0
);


CREATE TABLE IF NOT EXISTS product (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    barcode VARCHAR(128),
    product_name VARCHAR(256) NOT NULL,
    description TEXT,
    declaration TEXT,
    picture VARCHAR(256),
    pos VARCHAR(80),
    brand_name VARCHAR(50),
    weight VARCHAR(20),
    dimensions VARCHAR(50),
    category_id INTEGER NOT NULL,
    quantitymin FLOAT NOT NULL DEFAULT 0.0,
    uom VARCHAR(4),
    purchase_price FLOAT NOT NULL DEFAULT 0.0,
    trading_margin FLOAT NOT NULL DEFAULT 0.0,

    FOREIGN KEY (category_id) REFERENCES product_category(id)
);


CREATE TABLE IF NOT EXISTS inactive_product (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    product_id INTEGER NOT NULL UNIQUE,

    FOREIGN KEY (product_id) REFERENCES product(id)
);


CREATE TABLE IF NOT EXISTS partner (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    partner_name VARCHAR(128) NOT NULL,
    logo VARCHAR(256),
    id_number VARCHAR(13),
    tax_number VARCHAR(13),
    iban VARCHAR(34),
    address1 VARCHAR(80),
    address2 VARCHAR(80),
    region_state VARCHAR(80),
    zip VARCHAR(5),
    city VARCHAR(60),
    country VARCHAR(60),
    email VARCHAR(60),
    web VARCHAR(60),
    phone_number VARCHAR(20)
);


CREATE TABLE IF NOT EXISTS document (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    d_date DATE NOT NULL,
    d_type ENUM('purchase', 'sale', 'dismission') NOT NULL,
    d_status ENUM('draft', 'approved') NOT NULL,
    d_user INTEGER NOT NULL,
    d_partner INTEGER NOT NULL,
    discount FLOAT NOT NULL DEFAULT 0.0,

    FOREIGN KEY (d_user) REFERENCES ws_user(id),
    FOREIGN KEY (d_partner) REFERENCES partner(id)
);


CREATE TABLE IF NOT EXISTS document_product (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    document_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity FLOAT NOT NULL,

    FOREIGN KEY (document_id) REFERENCES document(id),
    FOREIGN KEY (product_id) REFERENCES product(id)
);
