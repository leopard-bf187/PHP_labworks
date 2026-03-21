# Лабораторная работа №5 — OOP в PHP

__Студент:__  *Пармакли Леонид IA2404ru*  
__Преподаватель лабораторных работ:__  *Вишневский Борис*  
__Преподаватель курса:__  *Нартя Никита*  


## Цель работы
Освоить основы объектно-ориентированного программирования в PHP на практике. Научиться создавать собственные классы, использовать инкапсуляцию для защиты данных, разделять ответственность между классами, а также применять интерфейсы для построения гибкой архитектуры приложения.

## Условие

Необходимо разработать приложение для управления банковскими транзакциями.
Приложение должно позволять:

- хранить банковские транзакции;
- добавлять новые транзакции;
- удалять транзакции;
- искать транзакции;
- сортировать транзакции;
- выполнять вычисления над коллекцией транзакций;
- выводить данные в виде HTML-таблицы.

## Что входит
- `index.php` — готовое решение лабораторной работы в одном файле.

## Как запустить
1. Откройте папку с файлом.
2. Выполните команду:
   ```bash
   php -S localhost:8000
   ```
3. В браузере откройте:
   ```
   http://localhost:8000/index.php
   ```

---
## Задание 1. Включение строгой типизации

В начале файла включите строгую типизацию:

```php
<?php
declare(strict_types=1);

/*...*/

?>
```

---

## Задание 2. Класс `Transaction`

Создайте класс Transaction, который описывает одну банковскую транзакцию. Класс должен содержать следующие свойства:

- id — уникальный идентификатор транзакции;
- date — дата транзакции;
- amount — сумма транзакции;
- description — описание платежа;
- merchant — получатель платежа.

```php
final class Transaction
{

    public function __construct
    (
        private int $id,
        private string $date,
        private float $amount,
        private string $description,
        private string $merchant
    ) 
    {
    }

    public function GetId(): int { /*...*/ }

    public function GetDate(): string  { /*...*/ }

    public function GetAmount(): float { /*...*/ }
    
    public function GetDescription(): string { /*...*/ }

    public function GetMerchant(): string { /*...*/ }

    public function GetDaysSinceTransaction(): int { /*...*/ }
}
```

---

## Задание 3. Класс `TransactionRepository`

Создайте класс TransactionRepository, который будет управлять коллекцией транзакций. Этот класс должен отвечать только за хранение данных и базовые операции доступа к ним. Класс должен:

1. хранить массив объектов Transaction;
2. добавлять новые транзакции;
   - addTransaction(Transaction $transaction): void
3. удалять транзакции по идентификатору;
   - removeTransactionById(int $id): void
4. возвращать полный список транзакций;
   - getAllTransactions(): array
5. находить транзакцию по id.
   - findById(int $id): ?Transaction

```php
final class TransactionRepository implements TransactionStorageInterface
{

    private array $transactions = [];

    public function AddTransaction(Transaction $transaction): void { /*...*/ }

    public function RemoveTransactionById(int $id): void { /*...*/ }

    public function GetAllTransactions(): array { /*...*/ }

    public function FindById(int $id): ?Transaction { /*...*/ }
}
```

---

## Задание 4. Класс `TransactionManager`

Создайте класс TransactionManager, который будет использовать TransactionRepository для выполнения бизнес-логики. TransactionManager не должен создавать транзакции самостоятельно и не должен хранить их внутри себя. Объект TransactionRepository необходимо передать в TransactionManager через конструктор.
Класс должен реализовать следующие функции:

1. вычисление общей суммы всех транзакций;
   - `calculateTotalAmount(): float`
2. вычисление суммы транзакций за определенный период;
   - `calculateTotalAmountByDateRange(string $startDate, string $endDate): float`
3. подсчет количества транзакций по определенному получателю;
   - `countTransactionsByMerchant(string $merchant): int`
4. сортировку транзакций по дате;
   - `sortTransactionsByDate(): Transaction[]`
5. сортировку транзакций по сумме по убыванию.
   - `sortTransactionsByAmountDesc(): Transaction[]`

```php
final class TransactionManager
{
    public function __construct(private TransactionStorageInterface $repository) 
    {}

    public function CalculateTotalAmount(): float { /*...*/ }

    public function CalculateTotalAmountByDateRange(string $startDate, string $endDate): float { /*...*/ }

    public function CountTransactionsByMerchant(string $merchant): int { /*...*/ }

    public function SortTransactionsByDate(): array { /*...*/ }

    public function SortTransactionsByAmountDesc(): array { /*...*/ }
}
```

---

## Задание 5. Класс `TransactionTableRenderer`

Создайте отдельный класс TransactionTableRenderer, который отвечает только за вывод транзакций в HTML. Этот класс должен получать список транзакций и формировать HTML-таблицу. Класс должен реализовать следующие функции:

`render(array $transactions): string — принимает массив транзакций и возвращает строку с HTML-кодом таблицы.`

Метод должен возвращать HTML-таблицу со следующими столбцами:

- ID транзакции;
- дата;
- сумма;
- описание;
- название получателя;
- категория получателя;
- количество дней с момента транзакции.

```php
final class TransactionTableRenderer
{
    public function Render(array $transactions): string
    {
        $rows = '';

        foreach ($transactions as $transaction) 
        {
            $rows .= sprintf
            (
                "<tr>\n" . 
                "   <td>%d</td>\n" . 
                "   <td>%s</td>\n" . 
                "   <td>%.2f</td>\n" . 
                "   <td>%s</td>\n" . 
                "   <td>%s</td>\n" . 
                "   <td>%s</td>\n" . 
                "   <td>%d</td>\n" . 
                "</tr>\n",
                $transaction->GetId(),
                $this->Escape($transaction->GetDate()),
                $transaction->GetAmount(),
                $this->Escape($transaction->GetDescription()),
                $this->Escape($transaction->GetMerchant()),
                $this->Escape($this->DetectMerchantCategory($transaction->GetMerchant())),
                $transaction->GetDaysSinceTransaction()
            );
        }

        return <<<htmlTableText
<table>
    <thead>
        <tr>
            <th>ID транзакции</th>
            <th>Дата</th>
            <th>Сумма</th>
            <th>Описание</th>
            <th>Получатель</th>
            <th>Категория получателя</th>
            <th>Дней с момента транзакции</th>
        </tr>
    </thead>
    <tbody>
        {$rows}
    </tbody>
</table>
htmlTableText;

    }

    private function DetectMerchantCategory(string $merchant): string { /*...*/ }

    private function Escape(string $value): string { /*...*/ }
}
```


---

## Задание 6. Начальные данные

Создайте не менее 10 объектов Transaction. Каждая транзакция должна содержать:

- разные даты;
- разные суммы;
- разные описания;
- разных получателей.

После создания объектов добавьте транзакции в TransactionRepository.

```php
$repository = new TransactionRepository();

$transactions = 
[
   new Transaction(1, '2025-09-03', 1250.50, 'Monthly apartment rent', 'City Home Rent'),
   /*...*/
   new Transaction(12, '2026-03-14', 145.00, 'Restaurant dinner', 'Sunset Restaurant'),
];

foreach ($transactions as $transaction) 
    $repository->AddTransaction($transaction);
```

---

## Задание 7. Интерфейс `TransactionStorageInterface`

После завершения основной реализации сделайте архитектуру более гибкой. Создайте интерфейс TransactionStorageInterface.Интерфейс должен содержать методы:

1. `addTransaction(Transaction $transaction): void`
2. `removeTransactionById(int $id): void`
3. `getAllTransactions(): array`
4. `findById(int $id): ?Transaction`

```php
interface TransactionStorageInterface
{
    public function AddTransaction(Transaction $transaction): void;

    public function RemoveTransactionById(int $id): void;

    public function GetAllTransactions(): array;

    public function FindById(int $id): ?Transaction;
}

/*...*/

    public function __construct(private TransactionStorageInterface $repository) 
    {
    }

/*...*/
```

---

## Контрольные вопросы

### 1. Зачем нужна строгая типизация в PHP и как она помогает при разработке?
Строгая типизация уменьшает количество скрытых ошибок, которые возникают из-за автоматического приведения типов. Она делает поведение программы более предсказуемым и упрощает отладку. Также она улучшает читаемость кода и помогает IDE точнее анализировать проект.

### 2. Что такое класс в объектно-ориентированном программировании и какие основные компоненты класса вы знаете?
Класс — это шаблон для создания объектов. Обычно он содержит свойства, методы, конструктор и модификаторы доступа. Через класс описывают состояние объекта и его поведение.

### 3. Объясните, что такое полиморфизм и как он может быть реализован в PHP.
Полиморфизм — это возможность работать с разными объектами через единый интерфейс. В PHP он часто реализуется через интерфейсы, абстрактные классы и переопределение методов. В этой лабораторной это видно на примере `TransactionStorageInterface`, через который менеджер работает с хранилищем.

### 4. Что такое интерфейс в PHP и как он отличается от абстрактного класса?
Интерфейс задаёт только контракт: какие методы класс обязан реализовать. Абстрактный класс может содержать и абстрактные методы, и готовую общую реализацию. Интерфейс удобен для слабой связанности, а абстрактный класс — для повторного использования общей логики.

### 5. Какие преимущества дает использование интерфейсов при проектировании архитектуры приложения? Объясните на примере данной лабораторной работы.
Интерфейсы снижают связанность между частями программы и упрощают замену реализации. В этой лабораторной `TransactionManager` зависит не от конкретного `TransactionRepository`, а от `TransactionStorageInterface`. Поэтому позже можно заменить хранилище, например, на файловое или базу данных, почти не меняя бизнес-логику.
