<h1>Список записей дневника настроения</h1>

<div class="button-group">
    <a href="?page=form&view=php" class="button-link">Добавить новую запись</a>
    <a href="?page=list&view=twig" class="button-link">Twig-версия</a>
</div>

<?php if (empty($entries)): ?>
    <div class="info-box">
        <p>Записей пока нет.</p>
    </div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th><a href="<?= sortLink('title', $sortField, $order, 'php') ?>">Заголовок</a></th>
            <th><a href="<?= sortLink('mood_date', $sortField, $order, 'php') ?>">Дата</a></th>
            <th><a href="<?= sortLink('mood_type', $sortField, $order, 'php') ?>">Настроение</a></th>
            <th><a href="<?= sortLink('energy_level', $sortField, $order, 'php') ?>">Энергия</a></th>
            <th>Теги</th>
            <th>Заметка</th>
            <th><a href="<?= sortLink('author', $sortField, $order, 'php') ?>">Автор</a></th>
            <th><a href="<?= sortLink('created_at', $sortField, $order, 'php') ?>">Создано</a></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($entries as $entry): ?>
            <tr>
                <td><?= hetemule($entry['title'] ?? '') ?></td>
                <td><?= hetemule($entry['mood_date'] ?? '') ?></td>
                <td><?= hetemule(moodIcon($entry['mood_type'] ?? '')) . ' ' . hetemule(moodLabel($entry['mood_type'] ?? '')) ?></td>
                <td><?= hetemule(energyLabel($entry['energy_level'] ?? '')) ?></td>
                <td><?= hetemule(implode(', ', $entry['tags'] ?? [])) ?></td>
                <td><?= hetemule($entry['note'] ?? '') ?></td>
                <td><?= hetemule($entry['author'] ?? '') ?></td>
                <td><?= hetemule($entry['created_at'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>