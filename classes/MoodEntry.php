<?php

/**
 * Class MoodEntry
 *
 * Модель одной записи дневника настроения.
 */
class MoodEntry
{
    /**
     * @var string Заголовок записи
     */
    private string $title;

    /**
     * @var string Дата настроения
     */
    private string $moodDate;

    /**
     * @var string Тип настроения
     */
    private string $moodType;

    /**
     * @var string Уровень энергии
     */
    private string $energyLevel;

    /**
     * @var string Подробная заметка
     */
    private string $note;

    /**
     * @var string Автор записи
     */
    private string $author;

    /**
     * @var string Дата и время создания записи
     */
    private string $createdAt;

    /**
     * @var array Список тегов
     */
    private array $tags;

    /**
     * MoodEntry конструктор.
     *
     * @param string $title Заголовок записи
     * @param string $moodDate Дата настроения
     * @param string $moodType Тип настроения
     * @param string $energyLevel Уровень энергии
     * @param string $note Подробная заметка
     * @param string $author Автор записи
     * @param string $createdAt Дата и время создания
     * @param array $tags Массив тегов
     */
    public function __construct(
        string $title,
        string $moodDate,
        string $moodType,
        string $energyLevel,
        string $note,
        string $author,
        string $createdAt,
        array $tags
    ) {
        $this->title = $title;
        $this->moodDate = $moodDate;
        $this->moodType = $moodType;
        $this->energyLevel = $energyLevel;
        $this->note = $note;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->tags = $tags;
    }

    /**
     * Преобразует объект в массив для сохранения.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'mood_date' => $this->moodDate,
            'mood_type' => $this->moodType,
            'energy_level' => $this->energyLevel,
            'note' => $this->note,
            'author' => $this->author,
            'created_at' => $this->createdAt,
            'tags' => $this->tags
        ];
    }
}




