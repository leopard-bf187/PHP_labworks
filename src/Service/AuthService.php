<?php

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Validator/AuthValidator.php';

/**
 * Class AuthService
 *
 * Реализует регистрацию, вход, выход и работу с текущим пользователем.
 */
class AuthService
{
    /**
     * @var UserRepository Репозиторий пользователей
     */
    private UserRepository $users;

    /**
     * @var AuthValidator Валидатор данных авторизации
     */
    private AuthValidator $validator;

    /**
     * AuthService constructor.
     *
     * @param UserRepository $users Репозиторий пользователей
     * @param AuthValidator $validator Валидатор
     */
    public function __construct(UserRepository $users, AuthValidator $validator)
    {
        $this->users = $users;
        $this->validator = $validator;
    }

    /**
     * Регистрирует нового пользователя.
     *
     * @param array $data Данные формы
     * @return array Результат операции
     */
    public function register(array $data): array
    {
        if (!$this->validator->validateRegister($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $username = trim($data['username']);
        $email = trim($data['email']);

        if ($this->users->existsByUsernameOrEmail($username, $email)) {
            return [
                'success' => false,
                'errors' => ['Пользователь с таким именем или email уже существует.']
            ];
        }

        $id = $this->users->create([
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'user',
        ]);

        return [
            'success' => true,
            'id' => $id,
            'errors' => []
        ];
    }

    /**
     * Создает администратора.
     *
     * @param array $data Данные формы
     * @return array Результат операции
     */
    public function createAdmin(array $data): array
    {
        if (!$this->validator->validateRegister($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $username = trim($data['username']);
        $email = trim($data['email']);

        if ($this->users->existsByUsernameOrEmail($username, $email)) {
            return [
                'success' => false,
                'errors' => ['Пользователь с таким именем или email уже существует.']
            ];
        }

        $id = $this->users->create([
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'admin',
        ]);

        return [
            'success' => true,
            'id' => $id,
            'errors' => []
        ];
    }

    /**
     * Выполняет вход пользователя.
     *
     * @param array $data Данные формы
     * @return array Результат операции
     */
    public function login(array $data): array
    {
        if (!$this->validator->validateLogin($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $username = trim($data['username']);
        $password = trim($data['password']);

        $user = $this->users->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'errors' => ['Неверное имя пользователя или пароль.']
            ];
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        return [
            'success' => true,
            'errors' => []
        ];
    }

    /**
     * Выполняет выход пользователя.
     *
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    /**
     * Возвращает текущего пользователя.
     *
     * @return array|null
     */
    public function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Проверяет, выполнен ли вход.
     *
     * @return bool
     */
    public function check(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Проверяет, является ли текущий пользователь администратором.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }
}