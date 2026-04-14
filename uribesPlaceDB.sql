create table if not EXISTS state(
                                    stateId integer PRIMARY Key AUTO_INCREMENT,
                                    stateName varchar(25)
);
INSERT INTO state (stateId, stateName) VALUES (1, 'Zulia');

create table if not EXISTS municipalities(
                                             municipalitiesId integer primary key AUTO_INCREMENT,
                                             municipalityName varchar(25),
                                             stateId integer REFERENCES state(stateId)
);
INSERT INTO municipalities (municipalitiesId, municipalityName, stateId) VALUES (1, 'Maracaibo', 1);

create table if not EXISTS parishes(
                                       parishiesId integer PRIMARY Key AUTO_INCREMENT,
                                       municipalitiesId integer REFERENCES municipalities(municipalitiesId),
                                       parishiesName varchar(25)
);

create table if not EXISTS category(
                                       idCategory integer primary Key AUTO_INCREMENT,
                                       categoryName varchar(50)
);

INSERT INTO category (categoryName) VALUES ('Farmacia');

create table if not EXISTS subcategory (
                                           subcategoryId integer primary key AUTO_INCREMENT,
                                           categoryId integer REFERENCES category(idCategory),
                                           subcategoryName varchar(50)
);

create table if not EXISTS productQuality (
                                              productQualityId integer PRIMARY key AUTO_INCREMENT,
                                              qualityName varchar(15)
);
INSERT INTO productQuality (productQualityId, qualityName) VALUES (1, 'Nuevo');

create table if not EXISTS deliveryStatus (
                                              deliveryStatusId integer PRIMARY key AUTO_INCREMENT,
                                              deliveryStatus varchar(15)
);

Create table if not EXISTS users(
                                    userId integer primary key AUTO_INCREMENT,
                                    nickname varchar(25) not null,
                                    firstName varchar(25) not null,
                                    lastName varchar(25),
                                    email varchar(25) not null,
                                    DNI varchar(15) not null,
                                    stateId integer REFERENCES state(stateId),
                                    municipalitiesId integer References municipalities(municipalitiesId),
                                    address varchar(255),
                                    zipCode varchar(10),
                                    phoneNumber varchar(15),
                                    createAt timestamp DEFAULT CURRENT_TIMESTAMP,
                                    modifiedAt timestamp DEFAULT CURRENT_TIMESTAMP
                                        ON update CURRENT_TIMESTAMP
);

alter table users add column password varchar(255) not null after email;
alter table users add column role varchar(20) not null default 'user' after password;
alter table users add column isActive boolean default true after role;
alter table users add column ImageUrl varchar(255) after modifiedAt;
ALTER TABLE users 
    ADD CONSTRAINT UNIQUE_DNI UNIQUE (DNI),
    ADD CONSTRAINT UNIQUE_EMAIL UNIQUE (email),
    ADD CONSTRAINT UNIQUE_NICKNAME UNIQUE (nickname);


INSERT INTO users (nickname, firstName, lastName, email, password, role, isActive, DNI, stateId, municipalitiesId, address, zipCode, phoneNumber)
VALUES (
    'usuario_prueba',
    'prueba',
    'prueba',
    'prueba@prueba.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- "password" encriptado
    'user',
    true,
    'V-12345678',
    1,
    1,
    'calle de pruebas',
    '4001',
    '04121234567'
);

create table if not exists store (
                                     storeId integer PRIMARY KEY AUTO_INCREMENT,
                                     idOwner integer REFERENCES users(userId),
                                     storeName varchar(50),
                                     storeDescription varchar(255),
                                     reputation double default 0,
                                     stateId integer REFERENCES state(stateId),
                                     municipalitiesId integer References municipalities(municipalitiesId),
                                     address varchar(255),
                                     zipCode varchar(10),
                                     phoneNumber varchar(15),
                                     createAt timestamp DEFAULT CURRENT_TIMESTAMP,
                                     modifiedAt timestamp DEFAULT CURRENT_TIMESTAMP
                                         ON update CURRENT_TIMESTAMP
);

alter table store add column ImageUrl varchar(255) after modifiedAt;
alter table store add column storeIsActive boolean default true after createAt;
alter table store add column category integer References category(categoryId) after reputation;

INSERT INTO store (idOwner, storeName, storeDescription, category,reputation, stateId, municipalitiesId, address, zipCode, phoneNumber)
VALUES (
    1,
    'tienda de prueba',
    'La mejor tienda de pruebas en Maracaibo',
    1,
    5.0,
    1,
    1,
    'C.C. Sambil, Nivel Feria',
    '4002',
    '02617000000'
);

CREATE TABLE IF NOT EXISTS store_ratings (
                                            idRating INTEGER PRIMARY KEY AUTO_INCREMENT,
                                            idStore INTEGER NOT NULL,
                                            idUser INTEGER NOT NULL,
                                            rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
                                            createAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                            modifiedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                            FOREIGN KEY (idStore) REFERENCES store(storeId) ON DELETE CASCADE,
                                            FOREIGN KEY (idUser) REFERENCES users(userId) ON DELETE CASCADE,
                                            UNIQUE KEY (idStore, idUser)
);

CREATE TABLE IF NOT EXISTS store_visits (
                                            idVisit INTEGER PRIMARY KEY AUTO_INCREMENT,
                                            idStore INTEGER NOT NULL,
                                            idUser INTEGER NULL,
                                            ipAddress VARCHAR(45),
                                            visitDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                            FOREIGN KEY (idStore) REFERENCES store(storeId) ON DELETE CASCADE
);

create table if not EXISTS products (
                                        idProduct integer Primary Key AUTO_INCREMENT,
                                        productName varchar(50),
                                        productDescription varchar(255),
                                        brand varchar(50),
                                        price double not null,
                                        idStore integer REFERENCES store(storeId),
                                        idProductQuality integer REFERENCES productQuality(productQualityId),
                                        stock integer,
                                        sellCount integer,
                                        SKU varchar(50),
                                        createAt timestamp DEFAULT CURRENT_TIMESTAMP,
                                        modifiedAt timestamp DEFAULT CURRENT_TIMESTAMP
                                            ON update CURRENT_TIMESTAMP
);

ALTER TABLE products ADD COLUMN isActive BOOLEAN DEFAULT TRUE AFTER SKU;

INSERT INTO products (productName, productDescription, brand, price, idStore, idProductQuality, stock, sellCount, SKU)
VALUES (
    'producto de prueba',
    'es de prueba',
    'prueba',
    850.99,
    1,
    1,
    15,
    0,
    'PRU-EBA-001'

);

CREATE TABLE IF NOT EXISTS product_images (
    idImage INTEGER PRIMARY KEY AUTO_INCREMENT,
    idProduct INTEGER NOT NULL,
    imageUrl VARCHAR(255) NOT NULL,
    isPrimary BOOLEAN DEFAULT FALSE,
    createAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idProduct) REFERENCES products(idProduct) ON DELETE CASCADE
);

ALTER TABLE product_images ADD COLUMN isActive BOOLEAN DEFAULT TRUE AFTER imageUrl;

create table if not EXISTS shoppingCart (
                                            cartId integer Primary Key AUTO_INCREMENT,
                                            productId integer REFERENCES products(idProduct),
                                            userId integer REFERENCES users(userId)
);

create table if not EXISTS orders (
                                      orderId integer PRIMARY KEY AUTO_INCREMENT,
                                      customerId integer REFERENCES users(userId),
                                      createAt timestamp DEFAULT CURRENT_TIMESTAMP,
                                      totalPrice double
);

create table if not exists orderItems (
                                          orderItemId integer PRIMARY KEY AUTO_INCREMENT,
                                          orderId integer REFERENCES orders(orderId),
                                          productId integer REFERENCES products(idProduct),
                                          quantity integer,
                                          price double
);

create table if not exists favoriteProducts (
                                                userId integer REFERENCES users(userId),
                                                idProduct integer REFERENCES products(idProduct)
);

create table if not exists storeFollow (
                                           userId integer REFERENCES users(userId),
                                           idStore integer REFERENCES store(storeId)
);


create table if not EXISTS delivery (
                                        idDelivery integer PRIMARY KEY AUTO_INCREMENT,
                                        deliveryStatusId integer REFERENCES deliveryStatus(deliveryStatusId),
                                        sendLocation varchar(255),
                                        deliveredLocation varchar(255),
                                        createAt timestamp default CURRENT_TIMESTAMP,
                                        deliveredAt timestamp DEFAULT CURRENT_TIMESTAMP
                                            on update CURRENT_TIMESTAMP
);


insert into storeFollow values(1,1);

insert into category (categoryName) values('Electrodomesticos');


create table if not exists activityLog(
    id integer primary key AUTO_INCREMENT,
    userId integer References users(userId),
    activityDescription varchar(255),
    createdAt timestamp default CURRENT_TIMESTAMP
    );

create table if not exists visitaSitio(
    idVisitante integer primary key AUTO_INCREMENT,
    horaEntrada timestamp default CURRENT_TIMESTAMP,
    tiempoEnSitio varchar(25)
);
