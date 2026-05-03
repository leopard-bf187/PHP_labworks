<?php

/**
 * Создает таблицы для дневника настроения.
 *
 * Таблицы:
 * - moods — справочник настроений;
 * - entries — записи дневника.
 *
 * Связь:
 * - moods.id -> entries.mood_id;
 * - одно настроение может быть связано со многими записями.
 *
 * @param PDO $pdo Подключение к базе данных
 * @return void
 */
function up(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS moods (
            id SERIAL PRIMARY KEY,
            code VARCHAR(32) NOT NULL UNIQUE,
            title VARCHAR(64) NOT NULL,
            icon VARCHAR(16) NOT NULL
        );
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS entries (
            id SERIAL PRIMARY KEY,
            mood_id INTEGER NOT NULL REFERENCES moods(id) ON DELETE RESTRICT,
            title VARCHAR(100) NOT NULL,
            mood_date DATE NOT NULL,
            energy_level VARCHAR(32) NOT NULL,
            note TEXT NOT NULL,
            author VARCHAR(50) NOT NULL,
            tags TEXT[] NOT NULL DEFAULT '{}',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
    ");

    $pdo->exec("
        INSERT INTO moods (code, title, icon)
        VALUES
            ('happy', 'Радостное', '😊'),
            ('calm', 'Спокойное', '😌'),
            ('sad', 'Грустное', '😢'),
            ('angry', 'Злое', '😠'),
            ('tired', 'Уставшее', '😴')
        ON CONFLICT (code) DO NOTHING;
    ");
}

/**
 * Удаляет таблицы дневника настроения.
 *
 * @param PDO $pdo Подключение к базе данных
 * @return void
 */
function down(PDO $pdo): void
{
    $pdo->exec("DROP TABLE IF EXISTS entries;");
    $pdo->exec("DROP TABLE IF EXISTS moods;");
}


