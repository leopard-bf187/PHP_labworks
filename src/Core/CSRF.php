<?php

/**
 * Class Csrf
 *
 * Реализует простую CSRF-защиту для POST-форм.
 */
class Csrf
{
    /**
     * Возвращает CSRF-токен текущей сессии.
     *
     * @return string
     */
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Проверяет CSRF-токен из формы.
     *
     * @param string|null $token Токен из POST-запроса
     * @return bool
     */
    public static function validate(?string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}


