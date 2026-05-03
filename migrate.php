<?php
/**

require_once __DIR__ . '/src/Core/Database.php';

$pdo = Database::getConnection();


$migrationFile = __DIR__ . '/migrations/001_create_mood_diary_tables.php';
if (!file_exists($migrationFile)) 
{
    echo "Файл миграции не найден.\n";
    exit(1);
}
require_once $migrationFile;



$migrationFile = __DIR__ . '/migrations/002_add_users_and_auth.php';
if (!file_exists($migrationFile)) 
{
    echo "Файл миграции не найден.\n";
    exit(1);
}
require_once $migrationFile;


up($pdo);

echo "Миграция успешно применена.\n";
*/

require_once __DIR__ . '/src/Core/Database.php';

$pdo = Database::getConnection();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS schema_migrations (
        id SERIAL PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
");

$files = glob(__DIR__ . '/migrations/*.php');
sort($files);

foreach ($files as $file) 
{
    $migrationName = basename($file);

    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM schema_migrations WHERE migration = :migration
    ");
    $stmt->execute([':migration' => $migrationName]);

    if ((int)$stmt->fetchColumn() > 0)
    {
        echo "Пропущена: {$migrationName}\n";
        continue;
    }

    $pdo->beginTransaction();

    try 
    {
        (function ($pdo, $file) 
        {
            require $file;
            up($pdo);
        })($pdo, $file);

        $stmt = $pdo->prepare("
            INSERT INTO schema_migrations (migration)
            VALUES (:migration)
        ");
        $stmt->execute([':migration' => $migrationName]);

        $pdo->commit();

        echo "Применена: {$migrationName}\n";
    } 
    catch (Throwable $e) 
    {
        $pdo->rollBack();
        echo "Ошибка: {$migrationName}: {$e->getMessage()}\n";
        exit(1);
    }
}


