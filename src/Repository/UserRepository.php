<?php

/**
 * Class UserRepository
 *
 * Выполняет операции с пользователями в базе данных.
 */
class UserRepository
{
    /**
     * @var PDO Подключение к базе данных
     */
    private PDO $pdo;

    /**
     * UserRepository constructor.
     *
     * @param PDO $pdo Подключение к PostgreSQL
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Создает нового пользователя.
     *
     * @param array $data Данные пользователя
     * @return int ID созданного пользователя
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO users (username, email, password_hash, role)
            VALUES (:username, :email, :password_hash, :role)
            RETURNING id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':role' => $data['role'] ?? 'user',
        ]);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Ищет пользователя по ID.
     *
     * @param int $id ID пользователя
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, username, email, password_hash, role, created_at, updated_at
            FROM users
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Ищет пользователя по имени.
     *
     * @param string $username Имя пользователя
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, username, email, password_hash, role, created_at, updated_at
            FROM users
            WHERE username = :username
        ");

        $stmt->execute([
            ':username' => $username
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Ищет пользователя по email.
     *
     * @param string $email Email пользователя
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, username, email, password_hash, role, created_at, updated_at
            FROM users
            WHERE email = :email
        ");

        $stmt->execute([
            ':email' => $email
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Возвращает всех пользователей.
     *
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, username, email, role, created_at, updated_at
            FROM users
            ORDER BY id ASC
        ");

        return $stmt->fetchAll();
    }

    /**
     * Удаляет пользователя по ID.
     *
     * @param int $id ID пользователя
     * @return bool
     */
    public function deleteById(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM users
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    /**
     * Проверяет существование пользователя с таким именем или email.
     *
     * @param string $username Имя пользователя
     * @param string $email Email
     * @return bool
     */
    public function existsByUsernameOrEmail(string $username, string $email): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM users
            WHERE username = :username OR email = :email
        ");

        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }
}


