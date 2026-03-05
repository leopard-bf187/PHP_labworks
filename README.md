# Лабораторная работа №4. Управляющие конструкции

__Студент:__  *Пармакли Леонид IA2404ru*  
__Преподаватель лабораторных работ:__  *Вишневский Борис*  
__Преподаватель курса:__  *Нартя Никита*  


---
## Цель работы
- Освоить работу с массивами в PHP, применяя различные операции: создание, добавление, удаление, сортировка и поиск. Закрепить навыки работы с функциями, включая передачу аргументов, возвращаемые значения и анонимные функции


---
## 1. Работа с массивом транзакций

Создан массив `$transactions`, где каждая транзакция содержит:

-   id
-   date
-   amount
-   description
-   merchant

``` php
$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart"
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant"
    ],
    [
        "id" => 3,
        "date" => "2021-11-20",
        "amount" => 250.00,
        "description" => "New headphones",
        "merchant" => "TechStore"
    ]
];
```

---

### 1. Подсчет общей суммы транзакций

``` php
function calculateTotalAmount(array $transactions): float {
    $sum = 0;
    foreach ($transactions as $t) {
        $sum += $t["amount"];
    }
    return $sum;
}
```

---

### 2. Поиск транзакции по части описания

``` php
function findTransactionByDescription(array $transactions, string $text): array {
    $result = [];

    foreach ($transactions as $t) {
        if (stripos($t["description"], $text) !== false) {
            $result[] = $t;
        }
    }

    return $result;
}
```

------------------------------------------------------------------------

### 3. Поиск транзакции по ID

### Через foreach

``` php
function findTransactionById(array $transactions, int $id): ?array {
    foreach ($transactions as $t) {
        if ($t["id"] === $id) {
            return $t;
        }
    }
    return null;
}
```

### Через array_filter

``` php
function findTransactionByIdFilter(array $transactions, int $id): ?array {
    $filtered = array_filter($transactions, fn($t) => $t["id"] === $id);

    if (count($filtered) == 0) {
        return null;
    }

    return array_values($filtered)[0];
}
```

------------------------------------------------------------------------

### 4. Количество дней с момента транзакции

``` php
function daysSinceTransaction(string $date): int {
    $transactionDate = new DateTime($date);
    $today = new DateTime();
    $diff = $today->diff($transactionDate);

    return $diff->days;
}
```

------------------------------------------------------------------------

### 5. Добавление новой транзакции

``` php
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void {

    global $transactions;

    $transactions[] = [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant
    ];
}
```

------------------------------------------------------------------------

### 6. Сортировка транзакций

### По дате

``` php
function sortTransactionsByDate(array $transactions): array {

    usort($transactions, function($a, $b) {
        return strcmp($a["date"], $b["date"]);
    });

    return $transactions;
}
```

### По сумме (убывание)

``` php
function sortTransactionsByAmountDesc(array $transactions): array {

    usort($transactions, function($a, $b) {
        return $b["amount"] <=> $a["amount"];
    });

    return $transactions;
}
```

------------------------------------------------------------------------

## 2. Галерея изображений

Необходимо создать папку `image/` и поместить туда 20--30 файлов `.jpg`.

Для получения списка изображений используется функция `scandir()`.

``` php
function getImages(string $dir): array {

    $files = scandir($dir);

    $images = [];

    foreach ($files as $file) {

        if ($file === "." || $file === "..") {
            continue;
        }

        if (preg_match("/\.jpg$/i", $file)) {
            $images[] = $file;
        }
    }

    return $images;
}
```

Полученные изображения выводятся на страницу в виде галереи.

``` php
foreach ($images as $img) {
    echo "<img src='image/$img' width='200'>";
}
```

---
# РЕЗУЛЬТАТ

![Транзакции](image\img00.png)

![Картинки](image\img01.png)



---

# Контрольные вопросы

## 1. Что такое массивы в PHP?

Массив - это структура данных, позволяющая хранить набор значений.Массивы могут быть:
-   индексными
-   ассоциативными
-   многомерными

---

## 2. Как можно создать массив?

Через квадратные скобки:

``` php
$array = [1, 2, 3];
```

или через функцию `array()`:

``` php
$array = array(1, 2, 3);
```

---

## 3. Для чего используется цикл foreach?

Цикл `foreach` используется для перебора элементов массива без
необходимости работать с индексами.

Пример:

``` php
foreach ($array as $value) {
    echo $value;
}
```