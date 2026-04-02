<?php

/**
 * Class Storage
 *
 * Отвечает за чтение и запись данных в JSON-файл.
 */
class Storage
{
    /**
     * @var string Путь к файлу хранения
     */
    private string $filePath;

    /**
     * Storage constructor.
     *
     * @param string $filePath Путь к JSON-файлу
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Сохраняет новую запись в файл.
     *
     * @param array $entry Данные записи
     * @return bool
     */
    public function save(array $entry): bool
    {
        $data = $this->readAll();
        $data[] = $entry;

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return file_put_contents($this->filePath, $json) !== false;
    }

    /**
     * Читает все записи из файла.
     *
     * @return array
     */
    public function readAll(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);

        if ($content === false || trim($content) === '') {
            return [];
        }

        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }
}



