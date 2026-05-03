<?php

/**
 * Class Middleware
 *
 * Содержит методы проверки доступа к защищённым разделам приложения.
 */
class Middleware
{
    /**
     * Проверяет, авторизован ли пользователь.
     *
     * @return void
     */
    public static function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Проверяет, является ли пользователь администратором.
     *
     * @return void
     */
    public static function requireAdmin(): void
    {
        self::requireAuth();

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo 'Доступ запрещён.';
            exit;
        }
    }

    /**
     * Возвращает ID текущего пользователя.
     *
     * @return int
     */
    public static function userId(): int
    {
        return (int)($_SESSION['user']['id'] ?? 0);
    }

    /**
     * Проверяет, является ли текущий пользователь администратором.
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return ($_SESSION['user']['role'] ?? '') === 'admin';
    }
}


