<?php

/**
 * Class MoodEntryValidator
 *
 * Выполняет серверную валидацию данных формы дневника настроения.
 */
class MoodEntryValidator
{
    /**
     * @var array Ошибки валидации
     */
    private array $errors = [];

    /**
     * @var array Допустимые уровни энергии
     */
    private array $allowedEnergyLevels = ['low', 'medium', 'high'];

    /**
     * @var array Допустимые теги
     */
    private array $allowedTags = ['study', 'work', 'family', 'sport', 'rest'];

    /**
     * Проверяет данные формы записи дневника.
     *
     * @param array $data Данные формы
     * @return bool
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        $title = trim($data['title'] ?? '');
        $moodId = (int)($data['mood_id'] ?? 0);
        $moodDate = trim($data['mood_date'] ?? '');
        $energyLevel = trim($data['energy_level'] ?? '');
        $note = trim($data['note'] ?? '');
        $author = trim($data['author'] ?? '');
        $tags = $data['tags'] ?? [];

        if ($title === '') {
            $this->errors[] = 'Поле "Заголовок" обязательно для заполнения.';
        } elseif (mb_strlen($title) < 3 || mb_strlen($title) > 100) {
            $this->errors[] = 'Заголовок должен содержать от 3 до 100 символов.';
        }

        if ($moodId <= 0) {
            $this->errors[] = 'Необходимо выбрать настроение.';
        }

        if ($moodDate === '') {
            $this->errors[] = 'Поле "Дата" обязательно для заполнения.';
        } elseif (!$this->isValidDate($moodDate)) {
            $this->errors[] = 'Дата настроения указана в неверном формате.';
        }

        if (!in_array($energyLevel, $this->allowedEnergyLevels, true)) {
            $this->errors[] = 'Выбран недопустимый уровень энергии.';
        }

        if ($note === '') {
            $this->errors[] = 'Поле "Заметка" обязательно для заполнения.';
        } elseif (mb_strlen($note) < 10 || mb_strlen($note) > 1000) {
            $this->errors[] = 'Заметка должна содержать от 10 до 1000 символов.';
        }

        if ($author === '') {
            $this->errors[] = 'Поле "Автор" обязательно для заполнения.';
        } elseif (mb_strlen($author) < 2 || mb_strlen($author) > 50) {
            $this->errors[] = 'Имя автора должно содержать от 2 до 50 символов.';
        }

        if (!is_array($tags)) {
            $this->errors[] = 'Поле "Теги" заполнено некорректно.';
        } else {
            foreach ($tags as $tag) {
                if (!in_array($tag, $this->allowedTags, true)) {
                    $this->errors[] = 'Обнаружен недопустимый тег.';
                    break;
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Возвращает ошибки валидации.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Проверяет дату в формате YYYY-MM-DD.
     *
     * @param string $date Дата
     * @return bool
     */
    private function isValidDate(string $date): bool
    {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);

        return $dateTime && $dateTime->format('Y-m-d') === $date;
    }
}

