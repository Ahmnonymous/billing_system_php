CREATE DATABASE IF NOT EXISTS bill;
USE bill;

CREATE TABLE IF NOT EXISTS emp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    salary DECIMAL(10, 2)
);

CREATE TABLE IF NOT EXISTS exp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    amount DECIMAL(10, 2),
    date DATE DEFAULT CURRENT_DATE
);

CREATE TABLE IF NOT EXISTS product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    rate DECIMAL(10, 2)
);

CREATE TABLE IF NOT EXISTS recm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recm_date DATE DEFAULT CURRENT_DATE,
    cust_name VARCHAR(255), 
    proj_name VARCHAR(255), 
    date DATE,
    phone VARCHAR(15),
    advance DECIMAL(10, 2),
    balance DECIMAL(10, 2),
    grand_total DECIMAL(10, 2)
);

CREATE TABLE IF NOT EXISTS rect (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE DEFAULT CURRENT_DATE,
    recm_id INT,
    prod_id INT,
    ser_no INT,
    height DECIMAL(10, 2),
    width DECIMAL(10, 2),
    sq_ft DECIMAL(10, 2),
    qty INT,
    total DECIMAL(10, 2),
    FOREIGN KEY (recm_id) REFERENCES recm(id),
    FOREIGN KEY (prod_id) REFERENCES product(id)
);

CREATE TABLE IF NOT EXISTS sal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT,
    date DATE,
    amount DECIMAL(10, 2),
    FOREIGN KEY (emp_id) REFERENCES emp(id)
);

CREATE TABLE IF NOT EXISTS atd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT,
    sal_id INT,
    date DATE,
    status CHAR(1) NOT NULL,
    FOREIGN KEY (emp_id) REFERENCES emp(id),
    FOREIGN KEY (sal_id) REFERENCES sal(id)
);


-- Insert data into the 'product' table
INSERT INTO product (name, category, rate)
VALUES
    -- Flex Material
    ('China 240grm', 'Flex Material', 10),
    ('China 240grm Matt', 'Flex Material', 20),
    ('Star', 'Flex Material', 30),
    ('Backlite', 'Flex Material', 40),
    ('Vnyile', 'Flex Material', 50),
    ('1 Vision', 'Flex Material', 60),
    
    -- Fitting Charges
    ('Pasting with Material', 'Fitting Charges', 70),
    ('Frontlite Board Making with Fitting', 'Fitting Charges', 10),
    ('Backlite Board Making with Fitting', 'Fitting Charges', 20),
    
    -- Visiting Card
    ('Shine', 'Visiting Card', 30),
    ('Matt', 'Visiting Card', 40),
    ('Matt Double Side', 'Visiting Card', 50),
    ('UV', 'Visiting Card', 20),
    ('UV Double Side', 'Visiting Card', 10),
    ('UV Pasting', 'Visiting Card', 50),
    
    -- Offset Material
    ('Bill Book - 1 Color', 'Offset Material', 30),
    ('Bill Book - 4 Color', 'Offset Material', 40),
    ('Bill Book - Carbonless', 'Offset Material', 60),
    ('Pad - 1 Color', 'Offset Material', 80),
    ('Pad - 4 Color', 'Offset Material', 70),
    ('Pamphlets - 1 Color', 'Offset Material', 90),
    ('Pamphlets - 2 Color', 'Offset Material', 10),
    ('Pamphlets - 4 Color', 'Offset Material', 30);
