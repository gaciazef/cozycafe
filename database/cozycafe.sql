CREATE DATABASE IF NOT EXISTS `cozycafe`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `cozycafe`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `order_change_histories`;
DROP TABLE IF EXISTS `cancellation_histories`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `cart_items`;
DROP TABLE IF EXISTS `carts`;
DROP TABLE IF EXISTS `menus`;
DROP TABLE IF EXISTS `cafe_tables`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `cafe_settings`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `menus` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `price` DECIMAL(12,2) NOT NULL,
    `image` VARCHAR(255) NULL,
    `availability_status` ENUM('Tersedia', 'Tidak Tersedia') NOT NULL DEFAULT 'Tersedia',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_menus_category` (`category_id`),
    KEY `idx_menus_availability` (`availability_status`),
    CONSTRAINT `fk_menus_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `chk_menus_price_positive` CHECK (`price` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cafe_tables` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `table_number` VARCHAR(30) NOT NULL,
    `area` ENUM('Indoor', 'Outdoor') NOT NULL,
    `capacity` INT UNSIGNED NOT NULL,
    `status` ENUM('Aktif', 'Tidak Aktif') NOT NULL DEFAULT 'Aktif',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_cafe_tables_number` (`table_number`),
    KEY `idx_cafe_tables_status_capacity` (`status`, `capacity`),
    CONSTRAINT `chk_cafe_tables_capacity` CHECK (`capacity` >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `carts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_carts_user` (`user_id`),
    CONSTRAINT `fk_carts_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cart_id` BIGINT UNSIGNED NOT NULL,
    `menu_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_cart_items_cart_menu` (`cart_id`, `menu_id`),
    KEY `idx_cart_items_menu` (`menu_id`),
    CONSTRAINT `fk_cart_items_cart`
        FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_cart_items_menu`
        FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `chk_cart_items_quantity` CHECK (`quantity` >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transactions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `transaction_code` VARCHAR(30) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `total_amount` DECIMAL(12,2) NOT NULL,
    `payment_status` ENUM('Belum Dibayar', 'Sudah Dibayar') NOT NULL DEFAULT 'Belum Dibayar',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_transactions_code` (`transaction_code`),
    KEY `idx_transactions_user` (`user_id`),
    KEY `idx_transactions_payment` (`payment_status`),
    CONSTRAINT `fk_transactions_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `chk_transactions_total` CHECK (`total_amount` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bookings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `transaction_id` BIGINT UNSIGNED NOT NULL,
    `table_id` BIGINT UNSIGNED NOT NULL,
    `booking_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `guest_count` INT UNSIGNED NOT NULL,
    `status` ENUM('Dikonfirmasi', 'Dibatalkan', 'Selesai') NOT NULL DEFAULT 'Dikonfirmasi',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_bookings_transaction` (`transaction_id`),
    KEY `idx_bookings_table_status` (`table_id`, `status`),
    KEY `idx_bookings_date_table_status` (`booking_date`, `table_id`, `status`),
    KEY `idx_bookings_date_start_end` (`booking_date`, `start_time`, `end_time`),
    CONSTRAINT `fk_bookings_transaction`
        FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_bookings_table`
        FOREIGN KEY (`table_id`) REFERENCES `cafe_tables` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `chk_bookings_guest_count` CHECK (`guest_count` >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `transaction_id` BIGINT UNSIGNED NOT NULL,
    `order_status` ENUM(
        'Menunggu Konfirmasi',
        'Dikonfirmasi',
        'Sedang Disiapkan',
        'Siap Disajikan',
        'Selesai',
        'Dibatalkan'
    ) NOT NULL DEFAULT 'Dikonfirmasi',
    `subtotal` DECIMAL(12,2) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_orders_transaction` (`transaction_id`),
    KEY `idx_orders_status` (`order_status`),
    CONSTRAINT `fk_orders_transaction`
        FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `chk_orders_subtotal` CHECK (`subtotal` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `menu_id` BIGINT UNSIGNED NULL,
    `menu_name_snapshot` VARCHAR(150) NOT NULL,
    `price_snapshot` DECIMAL(12,2) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL,
    `note` TEXT NULL,
    `subtotal` DECIMAL(12,2) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_order_items_order` (`order_id`),
    KEY `idx_order_items_menu` (`menu_id`),
    CONSTRAINT `fk_order_items_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_order_items_menu`
        FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `chk_order_items_quantity` CHECK (`quantity` >= 1),
    CONSTRAINT `chk_order_items_price` CHECK (`price_snapshot` >= 0),
    CONSTRAINT `chk_order_items_subtotal` CHECK (`subtotal` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `transaction_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_notifications_user_read` (`user_id`, `is_read`),
    KEY `idx_notifications_transaction` (`transaction_id`),
    CONSTRAINT `fk_notifications_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_notifications_transaction`
        FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cancellation_histories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `transaction_id` BIGINT UNSIGNED NOT NULL,
    `canceled_by` ENUM('customer', 'admin') NOT NULL,
    `reason` TEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cancellation_transaction` (`transaction_id`),
    CONSTRAINT `fk_cancellation_transaction`
        FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_change_histories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `admin_id` BIGINT UNSIGNED NOT NULL,
    `change_description` TEXT NOT NULL,
    `old_total` DECIMAL(12,2) NOT NULL,
    `new_total` DECIMAL(12,2) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_order_change_order` (`order_id`),
    KEY `idx_order_change_admin` (`admin_id`),
    CONSTRAINT `fk_order_change_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_order_change_admin`
        FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cafe_settings` (
    `id` TINYINT UNSIGNED NOT NULL,
    `cafe_name` VARCHAR(150) NOT NULL,
    `logo` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `address` VARCHAR(255) NULL,
    `phone` VARCHAR(30) NULL,
    `banner` VARCHAR(255) NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users`
    (`name`, `email`, `phone`, `password`, `role`)
VALUES
    (
        'Administrator CozyCafe',
        'admin@cozycafe.id',
        '081234567890',
        '$2y$10$RoY5EBJe7VZSufw.2wQrL.XpPbAZk7sdBT5XQO7GsSg6QqipaiwJy',
        'admin'
    );

INSERT INTO `cafe_settings`
    (`id`, `cafe_name`, `logo`, `description`, `address`, `phone`, `banner`)
VALUES
    (
        1,
        'CozyCafe',
        NULL,
        'Cafe nyaman untuk menikmati kopi, makanan favorit, dan suasana hangat.',
        'Jl. Contoh Cafe No. 1, Jakarta',
        '081234567890',
        NULL
    );

INSERT INTO `categories` (`name`, `description`) VALUES
    ('Coffee', 'Kopi panas dan dingin'),
    ('Non-Coffee', 'Minuman tanpa kopi'),
    ('Main Course', 'Makanan utama'),
    ('Snack', 'Camilan ringan'),
    ('Dessert', 'Makanan penutup');

INSERT INTO `cafe_tables` (`table_number`, `area`, `capacity`, `status`) VALUES
    ('I-01', 'Indoor', 2, 'Aktif'),
    ('I-02', 'Indoor', 4, 'Aktif'),
    ('I-03', 'Indoor', 6, 'Aktif'),
    ('O-01', 'Outdoor', 2, 'Aktif'),
    ('O-02', 'Outdoor', 4, 'Aktif'),
    ('O-03', 'Outdoor', 6, 'Tidak Aktif');

INSERT INTO `menus` (`category_id`, `name`, `description`, `price`, `image`, `availability_status`) VALUES
    ((SELECT `id` FROM `categories` WHERE `name` = 'Coffee'), 'Espresso', 'Espresso khas dengan rasa kopi yang kuat.', 22000.00, NULL, 'Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Coffee'), 'Cappuccino', 'Espresso dengan susu lembut dan foam.', 30000.00, NULL, 'Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Non-Coffee'), 'Matcha Latte', 'Minuman matcha dengan susu.', 32000.00, NULL, 'Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Non-Coffee'), 'Chocolate Milk', 'Cokelat susu yang lembut.', 28000.00, NULL, 'Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Main Course'), 'Chicken Steak', 'Steak ayam dengan kentang dan saus.', 65000.00, NULL, 'Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Main Course'), 'Creamy Pasta', 'Pasta dengan saus krim dan ayam.', 55000.00, NULL, 'Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Snack'), 'French Fries', 'Kentang goreng renyah.', 25000.00, NULL, 'Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Snack'), 'Onion Rings', 'Cincin bawang goreng renyah.', 27000.00, NULL, 'Tidak Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Dessert'), 'Tiramisu', 'Dessert kopi dengan lapisan lembut.', 38000.00, NULL, 'Tersedia'),
    ((SELECT `id` FROM `categories` WHERE `name` = 'Dessert'), 'Cheesecake', 'Kue keju lembut dengan saus buah.', 40000.00, NULL, 'Tersedia');
