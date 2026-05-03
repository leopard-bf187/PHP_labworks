<?php

/**
 * Class AuthValidator
 *
 * Проверяет данные форм регистрации, входа и создания администратора.
 */
class AuthValidator
{
    /**
     * @var array Ошибки валидации
     */
    private array $errors = [];

    /**
     * Проверяет форму регистрации.
     *
     * @param array $data Данные формы
     * @return bool
     */
    public function validateRegister(array $data): bool
    {
        $this->errors = [];

        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        $passwordConfirm = trim($data['password_confirm'] ?? '');

        if ($username === '') {
            $this->errors[] = 'Введите имя пользователя.';
        } elseif (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
            $this->errors[] = 'Имя пользователя должно содержать от 3 до 50 символов.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $this->errors[] = 'Имя пользователя может содержать только латинские буквы, цифры и подчёркивание.';
        }

        if ($email === '') {
            $this->errors[] = 'Введите email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Введите корректный email.';
        }

        if ($password === '') {
            $this->errors[] = 'Введите пароль.';
        } elseif (mb_strlen($password) < 6) {
            $this->errors[] = 'Пароль должен содержать минимум 6 символов.';
        }

        if ($password !== $passwordConfirm) {
            $this->errors[] = 'Пароли не совпадают.';
        }

        return empty($this->errors);
    }

    /**
     * Проверяет форму входа.
     *
     * @param array $data Данные формы
     * @return bool
     */
    public function validateLogin(array $data): bool
    {
        $this->errors = [];

        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($username === '') {
            $this->errors[] = 'Введите имя пользователя.';
        }

        if ($password === '') {
            $this->errors[] = 'Введите пароль.';
        }

        return empty($this->errors);
    }

    /**
     * Возвращает список ошибок.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

