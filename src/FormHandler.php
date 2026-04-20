<?php

require_once __DIR__ . '/MoodEntry.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Storage.php';

/**
 * Class FormHandler
 *
 * Управляет обработкой формы: валидацией, созданием объекта и сохранением.
 */
class FormHandler
{
    /**
     * @var Validator Объект валидатора
     */
    private Validator $validator;

    /**
     * @var Storage Объект хранилища
     */
    private Storage $storage;

    /**
     * FormHandler constructor.
     *
     * @param Validator $validator Валидатор
     * @param Storage $storage Хранилище
     */
    public function __construct(Validator $validator, Storage $storage)
    {
        $this->validator = $validator;
        $this->storage = $storage;
    }

    /**
     * Обрабатывает отправленные данные формы.
     *
     * @param array $postData Данные из $_POST
     * @return array
     */
    public function handle(array $postData): array
    {
        if (!$this->validator->validate($postData)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $entry = new MoodEntry(
            trim($postData['title']),
            trim($postData['mood_date']),
            trim($postData['mood_type']),
            trim($postData['energy_level']),
            trim($postData['note']),
            trim($postData['author']),
            date('Y-m-d H:i:s'),
            $postData['tags'] ?? []
        );

        $saved = $this->storage->save($entry->toArray());

        if (!$saved) {
            return [
                'success' => false,
                'errors' => ['Не удалось сохранить данные в файл.']
            ];
        }

        return [
            'success' => true,
            'errors' => []
        ];
    }
}





