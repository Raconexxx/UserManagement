<?php

declare(strict_types=1);

const BOOTSTRAP_DROP_TABLES = 1;
const BOOTSTRAP_TEST_USER = 1;

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

    if (BOOTSTRAP_TEST_USER === 1) {
        seedTestUser($pdo);
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
        'app_user_permissions',
        'app_group_permissions',
        'app_user_groups',
        'app_user_attributes',
        'app_user_contact_methods',
        'app_contact_types',
        'app_user_addresses',
        'app_user_emails',
        'app_user_profiles',
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

function seedTestUser(PDO $pdo): void
{
    $pdo->beginTransaction();

    try {
        $deleteUser = $pdo->prepare('DELETE FROM app_users WHERE username = :username');
        $deleteUser->execute(['username' => 'testuserin']);

        insertAdditionalTypes($pdo);
        insertPermissions($pdo);
        insertGroups($pdo);

        $insertUser = $pdo->prepare(
            'INSERT INTO app_users (username, password_hash) VALUES (:username, :password_hash)'
        );
        $insertUser->execute([
            'username' => 'testuserin',
            'password_hash' => password_hash('Test123!', PASSWORD_DEFAULT),
        ]);

        $usersId = (int) $pdo->lastInsertId();
        $groupsId = fetchId($pdo, 'app_groups', 'group_key', 'admins');

        $insertEmail = $pdo->prepare(
            'INSERT INTO app_users_emails
                (users_id, email, label, is_primary, login_enabled, verified_at)
             VALUES
                (:users_id, :email, :label, :is_primary, :login_enabled, CURRENT_TIMESTAMP)'
        );
        $insertEmail->execute([
            'users_id' => $usersId,
            'email' => 'testuserin@example.com',
            'label' => 'privat',
            'is_primary' => 1,
            'login_enabled' => 1,
        ]);
        $insertEmail->execute([
            'users_id' => $usersId,
            'email' => 'testuserin.arbeit@example.com',
            'label' => 'Arbeit',
            'is_primary' => 0,
            'login_enabled' => 1,
        ]);

        $insertAddress = $pdo->prepare(
            'INSERT INTO app_users_addresses
                (users_id, label, recipient_name, street, house_number, address_extra, postal_code, city, region, country_code, is_primary)
             VALUES
                (:users_id, :label, :recipient_name, :street, :house_number, :address_extra, :postal_code, :city, :region, :country_code, :is_primary)'
        );
        $insertAddress->execute([
            'users_id' => $usersId,
            'label' => 'privat',
            'recipient_name' => 'Test Benutzerin',
            'street' => 'Musterstraße',
            'house_number' => '12',
            'address_extra' => null,
            'postal_code' => '12345',
            'city' => 'Musterstadt',
            'region' => null,
            'country_code' => 'DE',
            'is_primary' => 1,
        ]);
        $insertAddress->execute([
            'users_id' => $usersId,
            'label' => 'Arbeit',
            'recipient_name' => 'Test Benutzerin',
            'street' => 'Büroallee',
            'house_number' => '7',
            'address_extra' => '2. OG',
            'postal_code' => '54321',
            'city' => 'Beispielstadt',
            'region' => null,
            'country_code' => 'DE',
            'is_primary' => 0,
        ]);

        insertUserAdditional($pdo, $usersId, 'display_name', 'Test Benutzerin', null, 1);
        insertUserAdditional($pdo, $usersId, 'birth_date', '2000-01-31', null, 0);
        insertUserAdditional($pdo, $usersId, 'mobile', '+491701234567', 'privat', 1);
        insertUserAdditional($pdo, $usersId, 'phone', '+49301234567', 'Büro', 0);
        insertUserAdditional($pdo, $usersId, 'website', 'https://example.com', 'Portfolio', 0);

        $insertAttribute = $pdo->prepare(
            'INSERT INTO app_users_attributes (users_id, attribute_key, attribute_value)
             VALUES (:users_id, :attribute_key, :attribute_value)'
        );
        $insertAttribute->execute([
            'users_id' => $usersId,
            'attribute_key' => 'department',
            'attribute_value' => 'Support',
        ]);

        $insertUserGroup = $pdo->prepare(
            'INSERT IGNORE INTO app_users_groups (users_id, groups_id) VALUES (:users_id, :groups_id)'
        );
        $insertUserGroup->execute([
            'users_id' => $usersId,
            'groups_id' => $groupsId,
        ]);

        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function insertAdditionalTypes(PDO $pdo): void
{
    $types = [
        'mobile' => 'Mobiltelefon',
        'phone' => 'Telefon',
        'website' => 'Webseite',
        'birth_date' => 'Geburtstag',
        'display_name' => 'Anzeigename',
    ];

    $insert = $pdo->prepare(
        'INSERT IGNORE INTO app_additional_types (type_key, name) VALUES (:type_key, :name)'
    );

    foreach ($types as $typeKey => $name) {
        $insert->execute([
            'type_key' => $typeKey,
            'name' => $name,
        ]);
    }
}

function insertPermissions(PDO $pdo): void
{
    $permissions = [
        'users.read' => 'Benutzer lesen',
        'users.write' => 'Benutzer bearbeiten',
        'groups.manage' => 'Gruppen verwalten',
        'permissions.manage' => 'Berechtigungen verwalten',
    ];

    $insertPermission = $pdo->prepare(
        'INSERT IGNORE INTO app_permissions (permission_key, name) VALUES (:permission_key, :name)'
    );

    foreach ($permissions as $permissionKey => $name) {
        $insertPermission->execute([
            'permission_key' => $permissionKey,
            'name' => $name,
        ]);
    }
}

function insertGroups(PDO $pdo): void
{
    $insertGroup = $pdo->prepare(
        'INSERT IGNORE INTO app_groups (group_key, name, description)
         VALUES (:group_key, :name, :description)'
    );
    $insertGroup->execute([
        'group_key' => 'admins',
        'name' => 'Administratoren',
        'description' => 'Voller Zugriff auf Benutzer, Gruppen und Berechtigungen.',
    ]);

    $groupsId = fetchId($pdo, 'app_groups', 'group_key', 'admins');

    $insertGroupPermission = $pdo->prepare(
        'INSERT IGNORE INTO app_groups_permissions (groups_id, permissions_id)
         VALUES (:groups_id, :permissions_id)'
    );

    foreach (['users.read', 'users.write', 'groups.manage', 'permissions.manage'] as $permissionKey) {
        $insertGroupPermission->execute([
            'groups_id' => $groupsId,
            'permissions_id' => fetchId($pdo, 'app_permissions', 'permission_key', $permissionKey),
        ]);
    }
}

function insertUserAdditional(PDO $pdo, int $usersId, string $typeKey, string $value, ?string $label, int $isPrimary): void
{
    $insert = $pdo->prepare(
        'INSERT INTO app_users_additionals
            (users_id, additional_types_id, additional_value, label, is_primary, verified_at)
         VALUES
            (:users_id, :additional_types_id, :additional_value, :label, :is_primary, CURRENT_TIMESTAMP)'
    );
    $insert->execute([
        'users_id' => $usersId,
        'additional_types_id' => fetchId($pdo, 'app_additional_types', 'type_key', $typeKey),
        'additional_value' => $value,
        'label' => $label,
        'is_primary' => $isPrimary,
    ]);
}

function fetchId(PDO $pdo, string $table, string $column, string $value): int
{
    $statement = $pdo->prepare(sprintf('SELECT id FROM `%s` WHERE `%s` = :value LIMIT 1', $table, $column));
    $statement->execute(['value' => $value]);

    $id = $statement->fetchColumn();

    if ($id === false) {
        throw new RuntimeException(sprintf('Datensatz nicht gefunden: %s.%s = %s', $table, $column, $value));
    }

    return (int) $id;
}
