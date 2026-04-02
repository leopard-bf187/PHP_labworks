<?php

require_once __DIR__ . '/classes/FormHandler.php';

$validator = new Validator();
$storage = new Storage(__DIR__ . '/data/moods.json');
$formHandler = new FormHandler($validator, $storage);

$result = $formHandler->handle($_POST);

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Leonid (@leopard_bf187) Parmacli" />
        <title>Результат отправки</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    
    <body>
        <div class="container">
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
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
                        
            <div class="button-group">
                <a href="index.php" class="button-link">Вернуться к форме</a>
                <a href="list.php" class="button-link">Посмотреть записи</a>
            </div>
        </div>
    </body>
</html>





