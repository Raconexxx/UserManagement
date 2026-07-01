<?php

declare(strict_types=1);

const BOOTSTRAP_DROP_TABLES = 1;

function bootstrapDatabase(PDO $pdo): void
{
    if (BOOTSTRAP_DROP_TABLES === 1) {
        dropAppTables($pdo);
    }

    $statements = [
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deactivated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_app_users_username (username),
    KEY idx_app_users_deactivated_at (deactivated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_users_emails (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    users_id INT UNSIGNED NOT NULL,
    email VARCHAR(190) NOT NULL,
    label VARCHAR(40) NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    login_enabled TINYINT(1) NOT NULL DEFAULT 1,
    verified_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_app_users_emails_email (email),
    KEY idx_app_users_emails_users_id (users_id),
    KEY idx_app_users_emails_login_enabled (login_enabled),
    CONSTRAINT fk_app_users_emails_users
        FOREIGN KEY (users_id) REFERENCES app_users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_users_addresses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    users_id INT UNSIGNED NOT NULL,
    label VARCHAR(40) NULL,
    recipient_name VARCHAR(160) NULL,
    street VARCHAR(190) NULL,
    house_number VARCHAR(40) NULL,
    address_extra VARCHAR(190) NULL,
    postal_code VARCHAR(40) NULL,
    city VARCHAR(120) NULL,
    region VARCHAR(120) NULL,
    country_code CHAR(2) NOT NULL DEFAULT 'DE',
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_app_users_addresses_users_id (users_id),
    CONSTRAINT fk_app_users_addresses_users
        FOREIGN KEY (users_id) REFERENCES app_users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_additional_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_key VARCHAR(60) NOT NULL,
    name VARCHAR(120) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_app_additional_types_type_key (type_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_users_additionals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    users_id INT UNSIGNED NOT NULL,
    additional_types_id INT UNSIGNED NOT NULL,
    additional_value VARCHAR(255) NOT NULL,
    label VARCHAR(80) NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    verified_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_app_users_additionals_users_id (users_id),
    KEY idx_app_users_additionals_additional_types_id (additional_types_id),
    CONSTRAINT fk_app_users_additionals_users
        FOREIGN KEY (users_id) REFERENCES app_users (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_app_users_additionals_additional_types
        FOREIGN KEY (additional_types_id) REFERENCES app_additional_types (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_users_attributes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    users_id INT UNSIGNED NOT NULL,
    attribute_key VARCHAR(120) NOT NULL,
    attribute_value TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_app_users_attributes_users_key (users_id, attribute_key),
    CONSTRAINT fk_app_users_attributes_users
        FOREIGN KEY (users_id) REFERENCES app_users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_key VARCHAR(80) NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deactivated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_app_groups_group_key (group_key),
    KEY idx_app_groups_deactivated_at (deactivated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    permission_key VARCHAR(120) NOT NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_app_permissions_permission_key (permission_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_users_groups (
    users_id INT UNSIGNED NOT NULL,
    groups_id INT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (users_id, groups_id),
    CONSTRAINT fk_app_users_groups_users
        FOREIGN KEY (users_id) REFERENCES app_users (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_app_users_groups_groups
        FOREIGN KEY (groups_id) REFERENCES app_groups (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_groups_permissions (
    groups_id INT UNSIGNED NOT NULL,
    permissions_id INT UNSIGNED NOT NULL,
    granted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (groups_id, permissions_id),
    CONSTRAINT fk_app_groups_permissions_groups
        FOREIGN KEY (groups_id) REFERENCES app_groups (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_app_groups_permissions_permissions
        FOREIGN KEY (permissions_id) REFERENCES app_permissions (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        <<<'SQL'
CREATE TABLE IF NOT EXISTS app_users_permissions (
    users_id INT UNSIGNED NOT NULL,
    permissions_id INT UNSIGNED NOT NULL,
    effect ENUM('allow', 'deny') NOT NULL DEFAULT 'allow',
    granted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (users_id, permissions_id),
    CONSTRAINT fk_app_users_permissions_users
        FOREIGN KEY (users_id) REFERENCES app_users (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_app_users_permissions_permissions
        FOREIGN KEY (permissions_id) REFERENCES app_permissions (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}

function dropAppTables(PDO $pdo): void
{
    $tables = [
        'app_users_permissions',
        'app_groups_permissions',
        'app_users_groups',
        'app_users_attributes',
        'app_users_additionals',
        'app_additional_types',
        'app_users_addresses',
        'app_users_emails',
        'app_permissions',
        'app_groups',
        'app_users',
    ];

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    try {
        foreach ($tables as $table) {
            $pdo->exec(sprintf('DROP TABLE IF EXISTS `%s`', $table));
        }
    } finally {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
}
