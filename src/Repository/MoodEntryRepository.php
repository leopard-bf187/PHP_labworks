<?php

/**
 * Class MoodEntryRepository
 *
 * Выполняет CRUD-операции для записей дневника настроения.
 */
class MoodEntryRepository
{
    /**
     * @var PDO Подключение к базе данных
     */
    private PDO $pdo;

    /**
     * MoodEntryRepository constructor.
     *
     * @param PDO $pdo Подключение к PostgreSQL
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Добавляет новую запись дневника.
     *
     * @param array $data Данные записи
     * @return int ID созданной записи
     */
    public function createRecord(array $data): int
    {
        $sql = "
            INSERT INTO entries (
                user_id,
                mood_id,
                title,
                mood_date,
                energy_level,
                note,
                author,
                tags
            )
            VALUES (
                :user_id,
                :mood_id,
                :title,
                :mood_date,
                :energy_level,
                :note,
                :author,
                :tags
            )
            RETURNING id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':mood_id' => $data['mood_id'],
            ':title' => $data['title'],
            ':mood_date' => $data['mood_date'],
            ':energy_level' => $data['energy_level'],
            ':note' => $data['note'],
            ':author' => $data['author'],
            ':tags' => $this->arrayToPgArray($data['tags'] ?? []),
        ]);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Получает записи пользователя.
     *
     * @param int $userId ID пользователя
     * @param string $sortField Поле сортировки
     * @param string $order Направление сортировки
     * @return array
     */
    public function getRecordsByUserId(int $userId, string $sortField = 'created_at', string $order = 'desc'): array
    {
        [$sortField, $order] = $this->normalizeSort($sortField, $order);

        $sql = "
            SELECT
                e.id,
                e.user_id,
                e.title,
                e.mood_date,
                e.energy_level,
                e.note,
                e.author,
                e.tags,
                e.created_at,
                e.updated_at,
                m.id AS mood_id,
                m.code AS mood_code,
                m.title AS mood_title,
                m.icon AS mood_icon,
                u.username AS username
            FROM entries e
            INNER JOIN moods m ON m.id = e.mood_id
            INNER JOIN users u ON u.id = e.user_id
            WHERE e.user_id = :user_id
            ORDER BY {$sortField} {$order}
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId
        ]);

        return $this->normalizeRecords($stmt->fetchAll());
    }

    /**
     * Получает все записи для администратора.
     *
     * @param string $sortField Поле сортировки
     * @param string $order Направление сортировки
     * @return array
     */
    public function getAllRecords(string $sortField = 'created_at', string $order = 'desc'): array
    {
        [$sortField, $order] = $this->normalizeSort($sortField, $order);

        $sql = "
            SELECT
                e.id,
                e.user_id,
                e.title,
                e.mood_date,
                e.energy_level,
                e.note,
                e.author,
                e.tags,
                e.created_at,
                e.updated_at,
                m.id AS mood_id,
                m.code AS mood_code,
                m.title AS mood_title,
                m.icon AS mood_icon,
                u.username AS username
            FROM entries e
            INNER JOIN moods m ON m.id = e.mood_id
            INNER JOIN users u ON u.id = e.user_id
            ORDER BY {$sortField} {$order}
        ";

        $stmt = $this->pdo->query($sql);

        return $this->normalizeRecords($stmt->fetchAll());
    }

    /**
     * Получает запись по ID.
     *
     * @param int $id ID записи
     * @return array|null
     */
    public function getRecordById(int $id): ?array
    {
        $sql = "
            SELECT
                e.id,
                e.user_id,
                e.title,
                e.mood_date,
                e.energy_level,
                e.note,
                e.author,
                e.tags,
                e.created_at,
                e.updated_at,
                m.id AS mood_id,
                m.code AS mood_code,
                m.title AS mood_title,
                m.icon AS mood_icon,
                u.username AS username
            FROM entries e
            INNER JOIN moods m ON m.id = e.mood_id
            INNER JOIN users u ON u.id = e.user_id
            WHERE e.id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $record = $stmt->fetch();

        if (!$record) {
            return null;
        }

        $records = $this->normalizeRecords([$record]);

        return $records[0];
    }

    /**
     * Получает запись по ID с проверкой владельца.
     *
     * @param int $id ID записи
     * @param int $userId ID пользователя
     * @return array|null
     */
    public function getRecordByIdAndUserId(int $id, int $userId): ?array
    {
        $record = $this->getRecordById($id);

        if (!$record) {
            return null;
        }

        if ((int)$record['user_id'] !== $userId) {
            return null;
        }

        return $record;
    }

    /**
     * Обновляет запись.
     *
     * @param int $id ID записи
     * @param array $data Новые данные
     * @return bool
     */
    public function updateRecord(int $id, array $data): bool
    {
        $sql = "
            UPDATE entries
            SET
                mood_id = :mood_id,
                title = :title,
                mood_date = :mood_date,
                energy_level = :energy_level,
                note = :note,
                author = :author,
                tags = :tags,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':mood_id' => $data['mood_id'],
            ':title' => $data['title'],
            ':mood_date' => $data['mood_date'],
            ':energy_level' => $data['energy_level'],
            ':note' => $data['note'],
            ':author' => $data['author'],
            ':tags' => $this->arrayToPgArray($data['tags'] ?? []),
        ]);
    }

    /**
     * Обновляет запись с проверкой владельца.
     *
     * @param int $id ID записи
     * @param int $userId ID пользователя
     * @param array $data Новые данные
     * @return bool
     */
    public function updateRecordByUserId(int $id, int $userId, array $data): bool
    {
        $sql = "
            UPDATE entries
            SET
                mood_id = :mood_id,
                title = :title,
                mood_date = :mood_date,
                energy_level = :energy_level,
                note = :note,
                author = :author,
                tags = :tags,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id AND user_id = :user_id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId,
            ':mood_id' => $data['mood_id'],
            ':title' => $data['title'],
            ':mood_date' => $data['mood_date'],
            ':energy_level' => $data['energy_level'],
            ':note' => $data['note'],
            ':author' => $data['author'],
            ':tags' => $this->arrayToPgArray($data['tags'] ?? []),
        ]);
    }

    /**
     * Удаляет запись по ID.
     *
     * @param int $id ID записи
     * @return bool
     */
    public function deleteRecord(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM entries
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    /**
     * Удаляет запись с проверкой владельца.
     *
     * @param int $id ID записи
     * @param int $userId ID пользователя
     * @return bool
     */
    public function deleteRecordByUserId(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM entries
            WHERE id = :id AND user_id = :user_id
        ");

        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId
        ]);
    }

    /**
     * Ищет записи пользователя.
     *
     * @param int $userId ID пользователя
     * @param string $query Поисковая строка
     * @return array
     */
    public function searchRecordsByUserId(int $userId, string $query): array
    {
        $sql = "
            SELECT
                e.id,
                e.user_id,
                e.title,
                e.mood_date,
                e.energy_level,
                e.note,
                e.author,
                e.tags,
                e.created_at,
                e.updated_at,
                m.id AS mood_id,
                m.code AS mood_code,
                m.title AS mood_title,
                m.icon AS mood_icon,
                u.username AS username
            FROM entries e
            INNER JOIN moods m ON m.id = e.mood_id
            INNER JOIN users u ON u.id = e.user_id
            WHERE
                e.user_id = :user_id AND
                (
                    e.title ILIKE :query OR
                    e.note ILIKE :query OR
                    e.author ILIKE :query OR
                    m.title ILIKE :query
                )
            ORDER BY e.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':query' => '%' . $query . '%'
        ]);

        return $this->normalizeRecords($stmt->fetchAll());
    }

    /**
     * Ищет записи по всем пользователям для администратора.
     *
     * @param string $query Поисковая строка
     * @return array
     */
    public function searchAllRecords(string $query): array
    {
        $sql = "
            SELECT
                e.id,
                e.user_id,
                e.title,
                e.mood_date,
                e.energy_level,
                e.note,
                e.author,
                e.tags,
                e.created_at,
                e.updated_at,
                m.id AS mood_id,
                m.code AS mood_code,
                m.title AS mood_title,
                m.icon AS mood_icon,
                u.username AS username
            FROM entries e
            INNER JOIN moods m ON m.id = e.mood_id
            INNER JOIN users u ON u.id = e.user_id
            WHERE
                e.title ILIKE :query OR
                e.note ILIKE :query OR
                e.author ILIKE :query OR
                m.title ILIKE :query OR
                u.username ILIKE :query
            ORDER BY e.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':query' => '%' . $query . '%'
        ]);

        return $this->normalizeRecords($stmt->fetchAll());
    }

    /**
     * Возвращает список настроений.
     *
     * @return array
     */
    public function getAllMoods(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, code, title, icon
            FROM moods
            ORDER BY id ASC
        ");

        return $stmt->fetchAll();
    }

    /**
     * Нормализует сортировку.
     *
     * @param string $sortField Поле сортировки
     * @param string $order Порядок сортировки
     * @return array
     */
    private function normalizeSort(string $sortField, string $order): array
    {
        $allowedSortFields = [
            'id',
            'title',
            'mood_date',
            'mood_title',
            'energy_level',
            'author',
            'username',
            'created_at',
            'updated_at'
        ];

        if (!in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'created_at';
        }

        $order = strtolower($order);

        if ($order !== 'asc' && $order !== 'desc') {
            $order = 'desc';
        }

        return [$sortField, $order];
    }

    /**
     * Нормализует список записей.
     *
     * @param array $records Записи из базы данных
     * @return array
     */
    private function normalizeRecords(array $records): array
    {
        foreach ($records as &$record) {
            $record['tags'] = $this->pgArrayToArray($record['tags'] ?? '{}');
        }

        return $records;
    }

    /**
     * Преобразует PHP-массив в PostgreSQL TEXT[].
     *
     * @param array $items Массив строк
     * @return string
     */
    private function arrayToPgArray(array $items): string
    {
        if (empty($items)) {
            return '{}';
        }

        $escaped = array_map(function ($item) {
            $item = str_replace('"', '\"', (string)$item);
            return '"' . $item . '"';
        }, $items);

        return '{' . implode(',', $escaped) . '}';
    }

    /**
     * Преобразует PostgreSQL TEXT[] в PHP-массив.
     *
     * @param string $value Значение PostgreSQL-массива
     * @return array
     */
    private function pgArrayToArray(string $value): array
    {
        $value = trim($value, '{}');

        if ($value === '') {
            return [];
        }

        return str_getcsv($value, ',', '"', '\\');
    }

    /**
     * Возвращает последние записи для публичной главной страницы.
     *
     * @param int $limit Количество записей
     * @return array
     */
    public function getLatestPublicRecords(int $limit = 5): array
    {
        $sql = "
            SELECT
                e.id,
                e.title,
                e.mood_date,
                e.note,
                e.created_at,
                m.title AS mood_title,
                m.icon AS mood_icon,
                u.username AS username
            FROM entries e
            INNER JOIN moods m ON m.id = e.mood_id
            INNER JOIN users u ON u.id = e.user_id
            ORDER BY e.created_at DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Возвращает статистику для публичной главной страницы.
     *
     * @return array
     */
    public function getPublicStats(): array
    {
        $totalStmt = $this->pdo->query("
            SELECT COUNT(*) 
            FROM entries
        ");
    
        $usersStmt = $this->pdo->query("
            SELECT COUNT(*) 
            FROM users
        ");
    
        $popularMoodStmt = $this->pdo->query("
            SELECT 
                m.title,
                m.icon,
                COUNT(e.id) AS count
            FROM moods m
            LEFT JOIN entries e ON e.mood_id = m.id
            GROUP BY m.id, m.title, m.icon
            ORDER BY count DESC
            LIMIT 1
        ");
    
        return [
            'total_entries' => (int)$totalStmt->fetchColumn(),
            'total_users' => (int)$usersStmt->fetchColumn(),
            'popular_mood' => $popularMoodStmt->fetch() ?: null,
        ];
    }
}



