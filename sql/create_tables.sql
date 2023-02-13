create table items
(
    id   int auto_increment
        primary key,
    name varchar(50) null
);

create table users
(
    id       int auto_increment
        primary key,
    login    varchar(100)              null,
    password varchar(100)              null,
    name     varchar(50)               null,
    surname  varchar(50)               null,
    address  varchar(70)               null,
    type     enum ('creator', 'taker') null
);

create table orders
(
    id        int auto_increment
        primary key,
    person_id int null,
    constraint orders_ibfk_1
        foreign key (person_id) references users (id)
);

create table order_items
(
    id       int auto_increment
        primary key,
    order_id int null,
    item_id  int null,
    amount   int null,
    constraint order_items_ibfk_1
        foreign key (order_id) references orders (id),
    constraint order_items_ibfk_2
        foreign key (item_id) references items (id)
);

create index item_id
    on order_items (item_id);

create index order_id
    on order_items (order_id);

create index person_id
    on orders (person_id);

create table taken_orders
(
    id        int auto_increment
        primary key,
    order_id  int                  null,
    person_id int                  null,
    resolved  tinyint(1) default 0 null,
    constraint taken_orders_ibfk_1
        foreign key (order_id) references orders (id),
    constraint taken_orders_ibfk_2
        foreign key (person_id) references users (id)
);

create index order_id
    on taken_orders (order_id);

create index person_id
    on taken_orders (person_id);
