CREATE TABLE IF NOT EXISTS company (
    id SERIAL PRIMARY KEY,
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
    id SERIAL PRIMARY KEY,
    category_name VARCHAR(80) NOT NULL,
    description TEXT,
    vat FLOAT NOT NULL DEFAULT 0.0,
    consumption_tax FLOAT NOT NULL DEFAULT 0.0,
    sales_tax FLOAT NOT NULL DEFAULT 0.0
);


CREATE TABLE IF NOT EXISTS product (
    id SERIAL PRIMARY KEY,
    barcode VARCHAR(128),
    product_name VARCHAR(256) NOT NULL,
    description TEXT,
    declaration TEXT,
    picture VARCHAR(256),
    pos VARCHAR(80),
    brand_name VARCHAR(50),
    weight VARCHAR(20),
    dimensions VARCHAR(50),
    category_id INTEGER NOT NULL REFERENCES product_category(id),
    quantitymin FLOAT NOT NULL DEFAULT 0.0,
    uom VARCHAR(4),
    purchase_price FLOAT NOT NULL DEFAULT 0.0,
    trading_margin FLOAT NOT NULL DEFAULT 0.0
);


CREATE TABLE IF NOT EXISTS inactive_product (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL UNIQUE REFERENCES product(id)
);


CREATE TABLE IF NOT EXISTS partner (
    id SERIAL PRIMARY KEY,
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


--create types
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'document_type') THEN
        CREATE TYPE DOCUMENT_TYPE AS ENUM ('purchase', 'sale', 'dismission');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'document_status') THEN
        CREATE TYPE DOCUMENT_STATUS AS ENUM ('draft', 'approved');
    END IF;
END$$;

CREATE TABLE IF NOT EXISTS document (
    id SERIAL PRIMARY KEY,
    d_date DATE NOT NULL DEFAULT NOW(),
    d_type DOCUMENT_TYPE NOT NULL,
    d_status DOCUMENT_STATUS NOT NULL,
    d_user INTEGER NOT NULL REFERENCES ws_user(id),
    d_partner INTEGER NOT NULL REFERENCES partner(id),
    discount FLOAT NOT NULL DEFAULT 0.0
);


CREATE TABLE IF NOT EXISTS document_product (
    id SERIAL PRIMARY KEY,
    document_id INTEGER NOT NULL REFERENCES document(id),
    product_id INTEGER NOT NULL REFERENCES product(id),
    quantity FLOAT NOT NULL
);
