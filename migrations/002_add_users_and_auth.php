<?php

/**
 * Добавляет пользователей, роли и связь записей с пользователями.
 *
 * @param PDO $pdo Подключение к базе данных
 * @return void
 */
function up(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(120) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'user',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

            CONSTRAINT users_role_check CHECK (role IN ('user', 'admin'))
        );
    ");

    $adminPasswordHash = password_hash('admin123', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password_hash, role)
        VALUES (:username, :email, :password_hash, :role)
        ON CONFLICT (username) DO NOTHING
    ");

    $stmt->execute([
        ':username' => 'admin',
        ':email' => 'admin@example.com',
        ':password_hash' => $adminPasswordHash,
        ':role' => 'admin',
    ]);

    $pdo->exec("
        ALTER TABLE entries
        ADD COLUMN IF NOT EXISTS user_id INTEGER;
    ");

    $stmt = $pdo->prepare("
        UPDATE entries
        SET user_id = (
            SELECT id FROM users WHERE username = 'admin' LIMIT 1
        )
        WHERE user_id IS NULL
    ");

    $stmt->execute();

    $pdo->exec("
        ALTER TABLE entries
        ALTER COLUMN user_id SET NOT NULL;
    ");

    $pdo->exec("
        DO $$
        BEGIN
            IF NOT EXISTS (
                SELECT 1
                FROM information_schema.table_constraints
                WHERE constraint_name = 'entries_user_id_fkey'
            ) THEN
                ALTER TABLE entries
                ADD CONSTRAINT entries_user_id_fkey
                FOREIGN KEY (user_id)
                REFERENCES users(id)
                ON DELETE CASCADE;
            END IF;
        END $$;
    ");
}

/**
 * Откатывает добавление пользователей и связи entries.user_id.
 *
 * @param PDO $pdo Подключение к базе данных
 * @return void
 */
function down(PDO $pdo): void
{
    $pdo->exec("
        ALTER TABLE entries
        DROP CONSTRAINT IF EXISTS entries_user_id_fkey;
    ");

    $pdo->exec("
        ALTER TABLE entries
        DROP COLUMN IF EXISTS user_id;
    ");

    $pdo->exec("
        DROP TABLE IF EXISTS users;
    ");
}