<?php

/**
 * Class Validator
 *
 * Выполняет серверную валидацию данных формы.
 */
class Validator
{
    /**
     * @var array Массив ошибок валидации
     */
    private array $errors = [];

    /**
     * Допустимые значения настроения.
     *
     * @var array
     */
    private array $allowedMoods = ['happy', 'calm', 'sad', 'angry', 'tired'];

    /**
     * Допустимые значения уровня энергии.
     *
     * @var array
     */
    private array $allowedEnergyLevels = ['low', 'medium', 'high'];

    /**
     * Допустимые теги.
     *
     * @var array
     */
    private array $allowedTags = ['study', 'work', 'family', 'sport', 'rest'];

    /**
     * Выполняет полную проверку данных формы.
     *
     * @param array $data Данные из формы
     * @return bool
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        $title = trim($data['title'] ?? '');
        $moodDate = trim($data['mood_date'] ?? '');
        $moodType = trim($data['mood_type'] ?? '');
        $energyLevel = trim($data['energy_level'] ?? '');
        $note = trim($data['note'] ?? '');
        $author = trim($data['author'] ?? '');
        $tags = $data['tags'] ?? [];

        if ($title === '') 
            $this->errors[] = 'Поле "Заголовок" обязательно для заполнения.';
        elseif (mb_strlen($title) < 3 || mb_strlen($title) > 100)
            $this->errors[] = 'Заголовок должен содержать от 3 до 100 символов.';


        if ($moodDate === '') 
            $this->errors[] = 'Поле "Дата" обязательно для заполнения.';
        elseif (!$this->isValidDate($moodDate)) 
            $this->errors[] = 'Дата настроения указана в неверном формате.';
        

        if (!in_array($moodType, $this->allowedMoods, true)) 
            $this->errors[] = 'Выбрано недопустимое значение настроения.';
        

        if (!in_array($energyLevel, $this->allowedEnergyLevels, true)) 
            $this->errors[] = 'Выбрано недопустимое значение уровня энергии.';
    

        if ($note === '') 
            $this->errors[] = 'Поле "Заметка" обязательно для заполнения.';
        elseif (mb_strlen($note) < 10 || mb_strlen($note) > 1000) 
            $this->errors[] = 'Заметка должна содержать от 10 до 1000 символов.';


        if ($author === '') 
            $this->errors[] = 'Поле "Автор" обязательно для заполнения.';
        elseif (mb_strlen($author) < 2 || mb_strlen($author) > 50) 
            $this->errors[] = 'Имя автора должно содержать от 2 до 50 символов.';
        

        if (!is_array($tags)) 
        {
            $this->errors[] = 'Поле "Теги" заполнено некорректно.';
        }
        else 
        {
            foreach ($tags as $tag) 
            {
                if (!in_array($tag, $this->allowedTags, true)) 
                {
                    $this->errors[] = 'Обнаружен недопустимый тег.';
                    break;
                }
            }
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

    /**
     * Проверяет корректность даты в формате YYYY-MM-DD.
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




