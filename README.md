# Лабораторная работа №6 — Обработка и валидация форм

__Студент:__  *Пармакли Леонид IA2404ru*  
__Преподаватель лабораторных работ:__  *Вишневский Борис*  
__Преподаватель курса:__  *Нартя Никита*  

## Цель работы

Освоить основные принципы работы с HTML-формами в PHP, включая отправку данных на сервер, обработку данных и их валидацию, согласно условиям лабораторной работы



## Как запустить

1. Открыть терминал в папке проекта.
2. Запустить встроенный сервер PHP:
    ```
    php -S localhost:8000
    ```
3. Открыть в браузере:
    ```
    http://localhost:8000/index.php
    ```



## Задание 1. Определение модели данных

В соответствии с условием лабораторной работы была выбрана тема «Дневник настроения».
На основе требований (минимум 6 полей, наличие string, date, enum, text) была разработана модель данных MoodEntry.

Используемые поля:

- `title` — строка (`string`);
- `mood_date` — дата (`date`);
- `mood_type` — тип настроения (`enum`);
- `energy_level` — уровень энергии (`enum`);
- `note` — подробное описание (`text`);
- `author` — автор (`string`);
- `created_at` — дата создания (`date`);
- `tags` — набор значений (`checkbox/enum`).

Объявление класса модели:
```php
class MoodEntry
{
    private string $title;
    private string $moodDate;
    private string $moodType;
    private string $energyLevel;
    private string $note;
    private string $author;
    private string $createdAt;
    private array $tags;

    public function __construct(...);
    public function toArray(): array;
}
```



## Задание 2. Создание HTML-формы

Согласно условию, была разработана HTML-форма для создания записи. Реализовано:
- метод отправки POST;
- поля, соответствующие модели данных;
- клиентская валидация через required, minlength, maxlength;
- элементы select для enum;
- checkbox для тегов;
- поле textarea для длинного текста.

Объявление формы:
```php
<form action="submit.php" method="POST">
```



## Задание 3. Обработка данных на сервере

В соответствии с заданием реализован PHP-скрипт обработки формы. Функциональность:
- получение данных через $_POST;
- серверная валидация;
- создание объекта модели;
- сохранение данных в файл JSON;
- возврат сообщений об ошибках или успехе.

Для этого реализованы отдельные классы.

Объявление валидатора:
```php
class Validator
{
    private array $errors;

    public function validate(array $data): bool;
    public function getErrors(): array;
}
```


Объявление хранилища
```php
class Storage
{
    private string $filePath;

    public function __construct(string $filePath);
    public function save(array $entry): bool;
    public function readAll(): array;
}
```



## Задание 4. Вывод данных

Согласно условию, реализован отдельный скрипт для отображения данных.

Функциональность:
- чтение данных из JSON-файла;
- отображение в HTML-таблице;
- форматирование данных;
- сортировка по полям (title, date, author, created_at).

Основная логика сортировки:
```php
usort($entries, function ($a, $b) use ($sortField, $order) { ... });
```



## Задание 5. ООП-реализация
Для получения максимальной оценки решение реализовано с использованием объектно-ориентированного подхода (как указано в условии). Такой подход улучшает читаемость кода расширяемость и разделение ответственности.

Были выделены следующие классы:

- `MoodEntry` — модель данных;
- `Validator` — валидация;
- `Storage` — работа с файлом;
- `FormHandler` — управление формой.

Объявление обработчика формы
```php
class FormHandler
{
    private Validator $validator;
    private Storage $storage;

    public function __construct(Validator $validator, Storage $storage);
    public function handle(array $postData): array;
}
```


### Скриншоты


![index.png](/images/index.png)
![submit.png](/images/submit.png)
![list.png](/images/list.png)




## Контрольные вопросы

### 1. Какие существуют методы отправки данных из формы на сервер? Какие методы поддерживает HTML-форма?
Основные методы — GET и POST.
- HTML-форма поддерживает оба метода через атрибут method.
- GET передает данные через URL, а POST — в теле запроса.

### 2. Какие глобальные переменные используются для доступа к данным формы в PHP?
Основные суперглобальные массивы:
- `$_GET` — данные из GET-запроса;
- `$_POST` — данные из POST-запроса;
- `$_REQUEST` — объединяет GET, POST и COOKIE.

### 3. Как обеспечить безопасность при обработке данных из формы (например, защититься от XSS)?
Для защиты от XSS необходимо:
- экранировать вывод с помощью htmlspecialchars();
- валидировать входные данные;
- не доверять данным пользователя;
--использовать строгие проверки значений (например, in_array для enum).
