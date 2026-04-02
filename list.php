<?php

require_once __DIR__ . '/classes/Storage.php';

$storage = new Storage(__DIR__ . '/data/moods.json');
$entries = $storage->readAll();

$sortField = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'desc';

$allowedSortFields = ['title', 'mood_date', 'mood_type', 'energy_level', 'author', 'created_at'];

if (!in_array($sortField, $allowedSortFields, true)) {
    $sortField = 'created_at';
}

if ($order !== 'asc' && $order !== 'desc') {
    $order = 'desc';
}

usort($entries, function ($a, $b) use ($sortField, $order) {
    $valueA = $a[$sortField] ?? '';
    $valueB = $b[$sortField] ?? '';

    if ($valueA == $valueB) {
        return 0;
    }

    if ($order === 'asc') {
        return $valueA <=> $valueB;
    }

    return $valueB <=> $valueA;
});

/**
 * Безопасно выводит строку.
 *
 * @param string $value
 * @return string
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Формирует ссылку для сортировки.
 *
 * @param string $field
 * @param string $currentField
 * @param string $currentOrder
 * @return string
 */
function sortLink(string $field, string $currentField, string $currentOrder): string
{
    $newOrder = 'asc';

    if ($field === $currentField && $currentOrder === 'asc') {
        $newOrder = 'desc';
    }

    return '?sort=' . urlencode($field) . '&order=' . urlencode($newOrder);
}
?>


<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Leonid (@leopard_bf187) Parmacli" />
        <title>Список записей дневника настроения</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    
    <body>
        <div class="container wide">
            <h1>Список записей дневника настроения</h1>

            <div class="button-group">
                <a href="index.php" class="button-link">Добавить новую запись</a>
            </div>

            <?php if (empty($entries)): ?>
                <div class="info-box">
                    <p>Записей пока нет.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                    <tr>
                        <th><a href="<?= sortLink('title', $sortField, $order) ?>">Заголовок</a></th>
                        <th><a href="<?= sortLink('mood_date', $sortField, $order) ?>">Дата</a></th>
                        <th><a href="<?= sortLink('mood_type', $sortField, $order) ?>">Настроение</a></th>
                        <th><a href="<?= sortLink('energy_level', $sortField, $order) ?>">Энергия</a></th>
                        <th>Теги</th>
                        <th>Заметка</th>
                        <th><a href="<?= sortLink('author', $sortField, $order) ?>">Автор</a></th>
                        <th><a href="<?= sortLink('created_at', $sortField, $order) ?>">Создано</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><?= e($entry['title'] ?? '') ?></td>
                            <td><?= e($entry['mood_date'] ?? '') ?></td>
                            <td><?= e($entry['mood_type'] ?? '') ?></td>
                            <td><?= e($entry['energy_level'] ?? '') ?></td>
                            <td><?= e(implode(', ', $entry['tags'] ?? [])) ?></td>
                            <td><?= e($entry['note'] ?? '') ?></td>
                            <td><?= e($entry['author'] ?? '') ?></td>
                            <td><?= e($entry['created_at'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </body>
</html>




