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


CREATE TABLE IF NOT EXISTS item_category (
    id SERIAL PRIMARY KEY,
    category_name VARCHAR(80) NOT NULL,
    description TEXT,
    vat FLOAT NOT NULL DEFAULT 0.0,
    consumption_tax FLOAT NOT NULL DEFAULT 0.0,
    sales_tax FLOAT NOT NULL DEFAULT 0.0
);


CREATE TABLE IF NOT EXISTS item (
    id SERIAL PRIMARY KEY,
    barcode VARCHAR(128),
    item_name VARCHAR(256) NOT NULL,
    description TEXT,
    declaration TEXT,
    picture VARCHAR(256),
    pos VARCHAR(80),
    category_id INTEGER NOT NULL REFERENCES item_category(id),
    quantitymin FLOAT NOT NULL DEFAULT 0.0,
    uom VARCHAR(4),
    purchase_price FLOAT NOT NULL DEFAULT 0.0,
    trading_margin FLOAT NOT NULL DEFAULT 0.0
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
    zip VARCHAR(5),
    city VARCHAR(60),
    country VARCHAR(60),
    email VARCHAR(60),
    web VARCHAR(60)
);


CREATE TYPE DOCUMENT_TYPE AS ENUM ('entrance', 'issue', 'sale');
CREATE TYPE DOCUMENT_STATUS AS ENUM ('draft', 'reviewed', 'approved');
CREATE TABLE IF NOT EXISTS document (
    id SERIAL PRIMARY KEY,
    d_date DATE NOT NULL DEFAULT NOW(),
    d_type DOCUMENT_TYPE NOT NULL,
    d_status DOCUMENT_STATUS NOT NULL,
    d_user INTEGER NOT NULL REFERENCES ws_user(id),
    d_partner INTEGER NOT NULL REFERENCES partner(id),
    discount FLOAT NOT NULL DEFAULT 0.0
);


CREATE TABLE IF NOT EXISTS document_item (
    id SERIAL PRIMARY KEY,
    document_id INTEGER NOT NULL REFERENCES document(id),
    item_id INTEGER NOT NULL REFERENCES item(id),
    quantity FLOAT NOT NULL
);
