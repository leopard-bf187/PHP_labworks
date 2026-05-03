<?php

/**
 * Class Captcha
 *
 * Простая CAPTCHA на основе арифметического выражения.
 */
class Captcha
{
    /**
     * Генерирует вопрос CAPTCHA и сохраняет ответ в сессию.
     *
     * @return string
     */
    public static function question(): string
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        $_SESSION['captcha_answer'] = (string)($a + $b);

        return "{$a} + {$b}";
    }

    /**
     * Проверяет ответ пользователя.
     *
     * @param string|null $answer Ответ из формы
     * @return bool
     */
    public static function validate(?string $answer): bool
    {
        if (empty($_SESSION['captcha_answer']) || $answer === null) {
            return false;
        }

        return trim($answer) === $_SESSION['captcha_answer'];
    }

    /**
     * Удаляет текущую CAPTCHA из сессии.
     *
     * @return void
     */
    public static function clear(): void
    {
        unset($_SESSION['captcha_answer']);
    }
}


