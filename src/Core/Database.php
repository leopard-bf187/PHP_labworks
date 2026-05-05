<?php

/**
 * Class Database
 *
 * Инкапсулирует подключение к PostgreSQL через PDO.
 */
class Database
{
    /**
     * @var PDO|null Экземпляр PDO
     */
    private static ?PDO $connection = null;

    /**
     * Возвращает подключение к базе данных.
     *
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config.php';
            $db = $config['db'];

            $dsn = sprintf(
                'pgsql:host=%s;port=%d;dbname=%s',
                $db['host'],
                $db['port'],
                $db['dbname']
            );

            self::$connection = new PDO(
                $dsn,
                $db['user'],
                $db['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            self::$connection->exec("SET NAMES '{$db['charset']}'");
        }

        return self::$connection;
    }
}


$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
