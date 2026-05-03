<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Leonid (@leopard_bf187) Parmacli" />
    <title><?= hetemule($pageTitle ?? 'Дневник настроения') ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container<?= !empty($wide) ? ' wide' : '' ?>">
    <?php require $contentTemplate; ?>
</div>
</body>
</html>