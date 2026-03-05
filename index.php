<?php
declare(strict_types=1);

/**
 * Лабораторная работа №4 — Массивы и функции
 * Задание 1: транзакции (массив + функции + сортировки + поиск)
 * Задание 2: галерея изображений (файловая система + scandir)
 */

/** ----------------------------
 * ЗАДАНИЕ 1. ТРАНЗАКЦИИ
 * ---------------------------- */

/**
 * Массив транзакций: каждая транзакция — ассоциативный массив:
 * id, date (YYYY-MM-DD), amount, description, merchant
 * :contentReference[oaicite:2]{index=2}
 */
$transactions = 
[
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ],
    [
        "id" => 3,
        "date" => "2021-11-20",
        "amount" => 250.00,
        "description" => "New headphones",
        "merchant" => "TechStore",
    ],
];

/**
 * Вычисляет общую сумму транзакций. :contentReference[oaicite:3]{index=3}
 *
 * @param array $transactions
 * @return float
 */
function calculateTotalAmount(array $transactions): float
{
    $sum = 0.0;
    foreach ($transactions as $t) 
        $sum += (float)$t["amount"];

    return $sum;
}

/**
 * Ищет транзакции по части описания (без учёта регистра). :contentReference[oaicite:4]{index=4}
 *
 * @param array $transactions
 * @param string $descriptionPart
 * @return array Найденные транзакции (массив)
 */
function findTransactionByDescription(array $transactions, string $descriptionPart): array
{
    $needle = mb_strtolower($descriptionPart);
    $found = [];

    foreach ($transactions as $t) 
    {
        $hay = mb_strtolower((string)$t["description"]);

        if (str_contains($hay, $needle)) 
            $found[] = $t;
    }

    return $found;
}

/**
 * Ищет транзакцию по id через foreach. :contentReference[oaicite:5]{index=5}
 *
 * @param array $transactions
 * @param int $id
 * @return array|null
 */
function findTransactionById_foreach(array $transactions, int $id): ?array
{
    foreach ($transactions as $t) 
        if ((int)$t["id"] === $id) 
            return $t;

    return null;
}

/**
 * Ищет транзакцию по id через array_filter (на высшую оценку). :contentReference[oaicite:6]{index=6}
 *
 * @param array $transactions
 * @param int $id
 * @return array|null
 */
function findTransactionById_filter(array $transactions, int $id): ?array
{
    $filtered = array_filter
    (
        $transactions,
        fn(array $t): bool => (int)$t["id"] === $id
    );

    if (count($filtered) === 0) 
        return null;

    // Берём первый элемент из отфильтрованных
    return array_values($filtered)[0];
}

/**
 * Возвращает количество дней между датой транзакции и сегодня. :contentReference[oaicite:7]{index=7}
 *
 * @param string $date YYYY-MM-DD
 * @return int
 */
function daysSinceTransaction(string $date): int
{
    $txDate = new DateTime($date);
    $today = new DateTime("today");
    $diff = $txDate->diff($today);

    // Если дата в будущем — вернётся 0 или отрицательное? diff даёт abs по умолчанию.
    return (int)$diff->days;
}

/**
 * Добавляет транзакцию в глобальный массив $transactions. :contentReference[oaicite:8]{index=8}
 *
 * @param int $id
 * @param string $date YYYY-MM-DD
 * @param float $amount
 * @param string $description
 * @param string $merchant
 * @return void
 */
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void
{
    global $transactions;

    $transactions[] = 
    [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant,
    ];
}

/**
 * Сортировка по дате (возрастание) через usort(). :contentReference[oaicite:9]{index=9}
 *
 * @param array $transactions
 * @return array
 */
function sortTransactionsByDate(array $transactions): array
{
    usort($transactions, function (array $a, array $b): int 
    {
        return strcmp((string)$a["date"], (string)$b["date"]);
    });
    return $transactions;
}

/**
 * Сортировка по сумме (убывание). :contentReference[oaicite:10]{index=10}
 *
 * @param array $transactions
 * @return array
 */
function sortTransactionsByAmountDesc(array $transactions): array
{
    usort($transactions, function (array $a, array $b): int {
        return ($b["amount"] <=> $a["amount"]);
    });
    return $transactions;
}

/** Добавим одну транзакцию (для демонстрации addTransaction) */
addTransaction(4, "2022-03-10", 19.99, "Coffee", "CoffeeBar");

/** Пример поиска */
$searchPart = "pay";
$foundByDesc = findTransactionByDescription($transactions, $searchPart);

/** Пример поиска по ID (оба способа) */
$foundId = 2;
$txForeach = findTransactionById_foreach($transactions, $foundId);
$txFilter  = findTransactionById_filter($transactions, $foundId);

/** Сортировки */
$sortedByDate = sortTransactionsByDate($transactions);
$sortedByAmt  = sortTransactionsByAmountDesc($transactions);

/** ----------------------------
 * ЗАДАНИЕ 2. ГАЛЕРЕЯ
 * ---------------------------- */

/**
 * Возвращает список .jpg файлов в директории (без . и ..).
 * :contentReference[oaicite:11]{index=11}
 *
 * @param string $dir
 * @return array
 */
function getJpgFiles(string $dir): array
{
    $files = scandir($dir);
    if ($files === false) 
        return [];

    $result = [];
    foreach ($files as $f) 
    {
        if ($f === "." || $f === "..") 
            continue;
        
        $path = rtrim($dir, "/\\") . DIRECTORY_SEPARATOR . $f;

        if (is_file($path) && preg_match('/\.jpe?g$/i', $f)) 
            $result[] = $f;
        
    }

    sort($result);
    return $result;
}

$imgDir = "image";
$images = is_dir($imgDir) ? getJpgFiles($imgDir) : [];

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>ЛР №4 — Массивы и функции</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        header, footer { padding: 12px 16px; background: #f2f2f2; border-radius: 8px; }
        nav a { margin-right: 12px; }
        table { border-collapse: collapse; width: 100%; margin: 12px 0 24px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #fafafa; }
        .note { color: #555; font-size: 14px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
        .grid img { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
        .section { margin-top: 24px; }
        code { background: #f6f6f6; padding: 2px 6px; border-radius: 6px; }
    </style>
</head>
<body>

<section id="task1" class="section">
    <h2>Задание 1 — Список транзакций</h2>
    <p class="note">
        Таблица включает столбец «Дней с момента транзакции» и итоговую сумму. :contentReference[oaicite:12]{index=12}
    </p>

    <h3>1) Транзакции (как есть)</h3>
    <?php renderTransactionsTable($transactions); ?>

    <h3>2) Сортировка по дате (возрастание)</h3>
    <?php renderTransactionsTable($sortedByDate); ?>

    <h3>3) Сортировка по сумме (убывание)</h3>
    <?php renderTransactionsTable($sortedByAmt); ?>

    <h3>4) Поиск по части описания: <code><?php echo htmlspecialchars($searchPart); ?></code></h3>
    <?php renderTransactionsTable($foundByDesc); ?>

    <h3>5) Поиск по ID = <code><?php echo (int)$foundId; ?></code></h3>
    <p class="note">foreach: <?php echo $txForeach ? "найдено" : "не найдено"; ?>, array_filter: <?php echo $txFilter ? "найдено" : "не найдено"; ?></p>
</section>

<section id="task2" class="section">
    <h2>Задание 2 — Галерея изображений</h2>
    <p class="note">
        Создайте папку <code>image/</code> и положите туда 20–30 файлов <code>.jpg</code>. :contentReference[oaicite:13]{index=13}
    </p>

    <?php if (!is_dir($imgDir)): ?>
        <p><b>Папка image/ не найдена.</b> Создайте <code>image</code> рядом с <code>index.php</code>.</p>
    <?php elseif (count($images) === 0): ?>
        <p><b>В папке image/ нет .jpg.</b> Добавьте несколько изображений.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($images as $img): ?>
                <img src="<?php echo htmlspecialchars($imgDir . "/" . $img); ?>" alt="<?php echo htmlspecialchars($img); ?>">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

</body>
</html>

<?php
/**
 * Рендерит HTML-таблицу транзакций + итоговую сумму.
 *
 * @param array $transactions
 * @return void
 */
function renderTransactionsTable(array $transactions): void
{
    $total = calculateTotalAmount($transactions);
    ?>
    <table>
        <thead>
        <tr>
            <th>id</th>
            <th>date</th>
            <th>amount</th>
            <th>description</th>
            <th>merchant</th>
            <th>days since</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?php echo (int)$t["id"]; ?></td>
                <td><?php echo htmlspecialchars((string)$t["date"]); ?></td>
                <td><?php echo number_format((float)$t["amount"], 2, ".", ""); ?></td>
                <td><?php echo htmlspecialchars((string)$t["description"]); ?></td>
                <td><?php echo htmlspecialchars((string)$t["merchant"]); ?></td>
                <td><?php echo daysSinceTransaction((string)$t["date"]); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            <th><?php echo number_format($total, 2, ".", ""); ?></th>
            <th colspan="3"></th>
        </tr>
        </tfoot>
    </table>
    <?php
}


