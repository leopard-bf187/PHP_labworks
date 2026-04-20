<h1>Результат обработки формы</h1>

<?php if ($result['success']): ?>
    <div class="success-box">
        <p>Запись успешно сохранена.</p>
    </div>
<?php else: ?>
    <div class="error-box">
        <p>Обнаружены ошибки:</p>
        <ul>
            <?php foreach ($result['errors'] as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="button-group">
    <a href="?page=form&view=php" class="button-link">Вернуться к форме</a>
    <a href="?page=list&view=php" class="button-link">Посмотреть записи</a>
    <a href="?page=form&view=twig" class="button-link">Twig-версия</a>
</div>