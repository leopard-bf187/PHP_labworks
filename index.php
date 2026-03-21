<?php

declare(strict_types=1);

/**
 * Интерфейс хранилища транзакций.
 */
interface TransactionStorageInterface
{
    /**
     * Добавляет транзакцию в хранилище.
     *
     * @param Transaction $transaction Транзакция для добавления.
     */
    public function AddTransaction(Transaction $transaction): void;

    /**
     * Удаляет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     */
    public function RemoveTransactionById(int $id): void;

    /**
     * Возвращает все транзакции.
     *
     * @return Transaction[]
     */
    public function GetAllTransactions(): array;

    /**
     * Ищет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return Transaction|null Найденная транзакция или null.
     */
    public function FindById(int $id): ?Transaction;
}

/**
 * Класс, описывающий банковскую транзакцию.
 */
final class Transaction
{
    /**
     * @param int $id Уникальный идентификатор.
     * @param string $date Дата транзакции в формате Y-m-d.
     * @param float $amount Сумма транзакции.
     * @param string $description Описание платежа.
     * @param string $merchant Получатель платежа.
     */
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

    /**
     * Возвращает идентификатор транзакции.
     */
    public function GetId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает дату транзакции.
     */
    public function GetDate(): string
    {
        return $this->date;
    }

    /**
     * Возвращает сумму транзакции.
     */
    public function GetAmount(): float
    {
        return $this->amount;
    }

    /**
     * Возвращает описание транзакции.
     */
    public function GetDescription(): string
    {
        return $this->description;
    }

    /**
     * Возвращает получателя платежа.
     */
    public function GetMerchant(): string
    {
        return $this->merchant;
    }

    /**
     * Возвращает количество дней с даты транзакции до текущего дня.
     */
    public function GetDaysSinceTransaction(): int
    {
        $transactionDate = new DateTime($this->date);
        $currentDate = new DateTime();

        return (int) $transactionDate->diff($currentDate)->days;
    }
}

/**
 * Репозиторий транзакций.
 */
final class TransactionRepository implements TransactionStorageInterface
{
    /**
     * @var Transaction[]
     */
    private array $transactions = [];

    /**
     * Добавляет транзакцию в хранилище.
     *
     * @param Transaction $transaction Транзакция для добавления.
     */
    public function AddTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    /**
     * Удаляет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     */
    public function RemoveTransactionById(int $id): void
    {
        foreach ($this->transactions as $index => $transaction)
        {
            if ($transaction->getId() === $id) 
                {
                unset($this->transactions[$index]);
                $this->transactions = array_values($this->transactions);
                return;
            }
        }
    }

    /**
     * Возвращает все транзакции.
     *
     * @return Transaction[]
     */
    public function GetAllTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * Ищет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return Transaction|null Найденная транзакция или null.
     */
    public function FindById(int $id): ?Transaction
    {
        foreach ($this->transactions as $transaction) 
            if ($transaction->getId() === $id) 
                return $transaction;

        return null;
    }
}

/**
 * Класс бизнес-логики для работы с транзакциями.
 */
final class TransactionManager
{
    /**
     * @param TransactionStorageInterface $repository Хранилище транзакций.
     */
    public function __construct(private TransactionStorageInterface $repository) 
    {
    }

    /**
     * Вычисляет общую сумму всех транзакций.
     */
    public function CalculateTotalAmount(): float
    {
        $total = 0.0;

        foreach ($this->repository->GetAllTransactions() as $transaction) 
            $total += $transaction->GetAmount();

        return $total;
    }

    /**
     * Вычисляет сумму транзакций в заданном диапазоне дат.
     *
     * @param string $startDate Начальная дата в формате Y-m-d.
     * @param string $endDate Конечная дата в формате Y-m-d.
     */
    public function CalculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $total = 0.0;

        foreach ($this->repository->GetAllTransactions() as $transaction) 
        {
            $transactionDate = new DateTime($transaction->GetDate());
            if ($transactionDate >= $start && $transactionDate <= $end) 
                $total += $transaction->GetAmount();
            
        }

        return $total;
    }

    /**
     * Подсчитывает количество транзакций по получателю.
     *
     * @param string $merchant Название получателя.
     */
    public function CountTransactionsByMerchant(string $merchant): int
    {
        $count = 0;

        foreach ($this->repository->GetAllTransactions() as $transaction) 
            if (mb_strtolower($transaction->GetMerchant()) === mb_strtolower($merchant)) 
                $count++;
            
        return $count;
    }

    /**
     * Сортирует транзакции по дате по возрастанию.
     *
     * @return Transaction[]
     */
    public function SortTransactionsByDate(): array
    {
        $transactions = $this->repository->GetAllTransactions();
        usort($transactions, static fn (Transaction $a, Transaction $b): int => strcmp($a->GetDate(), $b->GetDate()));
        return $transactions;
    }

    /**
     * Сортирует транзакции по сумме по убыванию.
     *
     * @return Transaction[]
     */
    public function SortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->GetAllTransactions();
        usort($transactions, static fn (Transaction $a, Transaction $b): int => $b->GetAmount() <=> $a->GetAmount());
        return $transactions;
    }
}

/**
 * Класс для генерации HTML-таблицы транзакций.
 */
final class TransactionTableRenderer
{
    /**
     * Формирует HTML-таблицу с транзакциями.
     *
     * @param Transaction[] $transactions Список транзакций.
     * @return string HTML-код таблицы.
     */
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

    /**
     * Определяет условную категорию получателя по его названию.
     *
     * @param string $merchant Название получателя.
     */
    private function DetectMerchantCategory(string $merchant): string
    {
        $normalized = mb_strtolower($merchant);

        return match (true) 
        {
            str_contains($normalized, 'market'),
            str_contains($normalized, 'supermarket'),
            str_contains($normalized, 'grocery') => 'Продукты',

            str_contains($normalized, 'pharmacy'),
            str_contains($normalized, 'med') => 'Здоровье',

            str_contains($normalized, 'fuel'),
            str_contains($normalized, 'gas'),
            str_contains($normalized, 'taxi') => 'Транспорт',

            str_contains($normalized, 'cafe'),
            str_contains($normalized, 'coffee'),
            str_contains($normalized, 'restaurant') => 'Питание',

            str_contains($normalized, 'tech'),
            str_contains($normalized, 'electronics') => 'Техника',

            str_contains($normalized, 'cinema'),
            str_contains($normalized, 'stream') => 'Развлечения',

            str_contains($normalized, 'utility'),
            str_contains($normalized, 'water'),
            str_contains($normalized, 'electric') => 'Коммунальные услуги',

            default => 'Другое',
        };
    }

    /**
     * Экранирует строку для безопасного вывода в HTML.
     *
     * @param string $value Входная строка.
     */
    private function Escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        //return $value;
    }
}

$repository = new TransactionRepository();

$transactions = 
[
    new Transaction(1, '2025-09-03', 1250.50, 'Monthly apartment rent', 'City Home Rent'),
    new Transaction(2, '2025-09-08', 84.90, 'Weekly groceries', 'Green Market'),
    new Transaction(3, '2025-09-14', 39.99, 'Music streaming subscription', 'StreamWave'),
    new Transaction(4, '2025-10-02', 420.00, 'New headphones', 'TechStore Electronics'),
    new Transaction(5, '2025-10-17', 61.30, 'Pharmacy purchase', 'Good Pharmacy'),
    new Transaction(6, '2025-11-01', 73.45, 'Fuel for car', 'Fast Fuel'),
    new Transaction(7, '2025-11-13', 26.80, 'Coffee with friends', 'Coffee Corner'),
    new Transaction(8, '2025-12-05', 110.00, 'Electricity bill', 'Electric Utility'),
    new Transaction(9, '2026-01-09', 58.20, 'Taxi to airport', 'City Taxi'),
    new Transaction(10, '2026-02-11', 47.75, 'Cinema tickets', 'Galaxy Cinema'),
    new Transaction(11, '2026-03-01', 92.10, 'Family supermarket shopping', 'SuperMarket Plus'),
    new Transaction(12, '2026-03-14', 145.00, 'Restaurant dinner', 'Sunset Restaurant'),
];

foreach ($transactions as $transaction) 
    $repository->AddTransaction($transaction);


$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();

$foundTransaction = $repository->FindById(4);
$transactionsByDate = $manager->SortTransactionsByDate();
$transactionsByAmount = $manager->SortTransactionsByAmountDesc();
$totalAmount = $manager->CalculateTotalAmount();
$totalForPeriod = $manager->CalculateTotalAmountByDateRange('2025-11-01', '2026-03-31');
$marketCount = $manager->CountTransactionsByMerchant('Green Market');

$demoRepository = new TransactionRepository();
foreach ($transactions as $transaction) 
    $demoRepository->AddTransaction($transaction);

$demoRepository->RemoveTransactionById(3);
$afterDeleteCount = count($demoRepository->GetAllTransactions());

?>



<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Leonid (@leopard_bf187) Parmacli" />
        <title>Лабораторная работа №5 — OOP в PHP</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 24px;
                background: #f5f7fb;
                color: #1f2937;
            }
            h1, h2 {
                margin-bottom: 12px;
            }
            .card {
                background: #ffffff;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 12px;
            }
            th, td {
                border: 1px solid #d1d5db;
                padding: 10px;
                text-align: left;
            }
            th {
                background: #e5eefc;
            }
            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                gap: 16px;
            }
            .metric {
                font-size: 15px;
                line-height: 1.7;
            }
            code {
                background: #eef2ff;
                padding: 2px 6px;
                border-radius: 6px;
            }
        </style>
    </head>
    <body>
        <h1>Лабораторная работа №5 — ООП PHP</h1>

        <div class="card">
            <h2>Краткая проверка требований</h2>
            <div class="grid">
                <div class="metric"><strong>Всего транзакций:</strong> <?= count($repository->GetAllTransactions()) ?></div>
                <div class="metric"><strong>Общая сумма:</strong> <?= number_format($totalAmount, 2, '.', ' ') ?></div>
                <div class="metric"><strong>Сумма за период 2025-11-01 — 2026-03-31:</strong> <?= number_format($totalForPeriod, 2, '.', ' ') ?></div>
                <div class="metric"><strong>Найденная транзакция по ID=4:</strong> <?= $foundTransaction?->GetDescription() ?? 'Не найдена' ?></div>
                <div class="metric"><strong>Количество транзакций у Green Market:</strong> <?= $marketCount ?></div>
                <div class="metric"><strong>После удаления ID=3 в тестовом репозитории:</strong> <?= $afterDeleteCount ?> записей</div>
            </div>
        </div>

        <div class="card">
            <h2>Все транзакции</h2>
            <?= $renderer->Render($repository->GetAllTransactions()) ?>
        </div>

        <div class="card">
            <h2>Сортировка по дате</h2>
            <?= $renderer->Render($transactionsByDate) ?>
        </div>

        <div class="card">
            <h2>Сортировка по сумме (по убыванию)</h2>
            <?= $renderer->Render($transactionsByAmount) ?>
        </div>

    </body>
</html>

