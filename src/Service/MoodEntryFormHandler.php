<?php

require_once __DIR__ . '/../Repository/MoodEntryRepository.php';
require_once __DIR__ . '/../Validator/MoodEntryValidator.php';

/**
 * Class MoodEntryFormHandler
 *
 * Обрабатывает создание и редактирование записей дневника настроения.
 */
class MoodEntryFormHandler
{
    /**
     * @var MoodEntryValidator Валидатор записей
     */
    private MoodEntryValidator $validator;

    /**
     * @var MoodEntryRepository Репозиторий записей
     */
    private MoodEntryRepository $repository;

    /**
     * MoodEntryFormHandler constructor.
     *
     * @param MoodEntryValidator $validator Валидатор
     * @param MoodEntryRepository $repository Репозиторий
     */
    public function __construct(MoodEntryValidator $validator, MoodEntryRepository $repository)
    {
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     * Создает новую запись.
     *
     * @param int $userId ID текущего пользователя
     * @param array $postData Данные формы
     * @return array
     */
    public function create(int $userId, array $postData): array
    {
        if (!$this->validator->validate($postData)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $data = $this->normalizeData($postData);
        $data['user_id'] = $userId;

        $id = $this->repository->createRecord($data);

        return [
            'success' => true,
            'id' => $id,
            'errors' => []
        ];
    }

    /**
     * Обновляет запись.
     *
     * @param int $entryId ID записи
     * @param int $userId ID текущего пользователя
     * @param bool $isAdmin Является ли пользователь администратором
     * @param array $postData Данные формы
     * @return array
     */
    public function update(int $entryId, int $userId, bool $isAdmin, array $postData): array
    {
        if (!$this->validator->validate($postData)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $data = $this->normalizeData($postData);

        if ($isAdmin) {
            $updated = $this->repository->updateRecord($entryId, $data);
        } else {
            $updated = $this->repository->updateRecordByUserId($entryId, $userId, $data);
        }

        return [
            'success' => $updated,
            'id' => $entryId,
            'errors' => $updated ? [] : ['Не удалось обновить запись или доступ запрещён.']
        ];
    }

    /**
     * Приводит данные формы к формату репозитория.
     *
     * @param array $postData Данные формы
     * @return array
     */
    private function normalizeData(array $postData): array
    {
        return [
            'mood_id' => (int)($postData['mood_id'] ?? 0),
            'title' => trim($postData['title'] ?? ''),
            'mood_date' => trim($postData['mood_date'] ?? ''),
            'energy_level' => trim($postData['energy_level'] ?? ''),
            'note' => trim($postData['note'] ?? ''),
            'author' => trim($postData['author'] ?? ''),
            'tags' => $postData['tags'] ?? [],
        ];
    }
}



