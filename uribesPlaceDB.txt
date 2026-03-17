create table if not EXISTS state(
	stateId integer PRIMARY Key AUTO_INCREMENT,
    stateName varchar(25)
); 

create table if not EXISTS municipalities(
	municipalitiesId integer primary key AUTO_INCREMENT,
    municipalityName varchar(25),
    stateId integer REFERENCES state(stateId)
);

create table if not EXISTS parishes(
	parishiesId integer PRIMARY Key AUTO_INCREMENT,
    municipalitiesId integer REFERENCES municipalities(municipalitiesId),
    parishiesName varchar(25)
);

create table if not EXISTS category(
	idCategory integer primary Key AUTO_INCREMENT,
    categoryName varchar(50)
);

create table if not EXISTS subcategory (
	subcategoryId integer primary key AUTO_INCREMENT,
    categoryId integer REFERENCES category(idCategory),
    subcategoryName varchar(50)
);

create table if not EXISTS productQuality (
	productQualityId integer PRIMARY key AUTO_INCREMENT,
    qualityName varchar(15)
);

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