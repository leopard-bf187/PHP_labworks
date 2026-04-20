<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/FormHandler.php';
require_once __DIR__ . '/../src/Storage.php';
require_once __DIR__ . '/../src/Validator.php';
require_once __DIR__ . '/../src/helpers.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

$page = $_GET['page'] ?? 'form';
$view = $_GET['view'] ?? 'php';

$storage = new Storage(__DIR__ . '/../data/moods.json');

$sortField = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'desc';
$allowedSortFields = ['title', 'mood_date', 'mood_type', 'energy_level', 'author', 'created_at'];

if (!in_array($sortField, $allowedSortFields, true)) 
    $sortField = 'created_at';


if ($order !== 'asc' && $order !== 'desc') 
    $order = 'desc';


$entries = $storage->readAll();


usort($entries, function ($a, $b) use ($sortField, $order) 
{
    $valueA = $a[$sortField] ?? '';
    $valueB = $b[$sortField] ?? '';

    if ($valueA == $valueB) 
        return 0;
    
    return $order === 'asc' ? ($valueA <=> $valueB) : ($valueB <=> $valueA);
});


function nextOrder(string $field, string $currentField, string $currentOrder): string
{
    if ($field === $currentField && $currentOrder === 'asc') 
        return 'desc';
    
    return 'asc';
}



if ($view === 'twig') 
{
    $loader = new FilesystemLoader(__DIR__ . '/../templates_twig');
    $twig = new Environment($loader);

    $twig->addFilter(new TwigFilter('mood_icon', fn(string $mood) => moodIcon($mood)));
    $twig->addFilter(new TwigFilter('mood_label', fn(string $mood) => moodLabel($mood)));
    $twig->addFilter(new TwigFilter('energy_label', fn(string $level) => energyLabel($level)));

    switch ($page) 
    {
        case 'submit':
            $handler = new FormHandler(new Validator(), $storage);
            $result = $handler->handle($_POST);
            echo $twig->render('result.twig', ['result' => $result]);
            break;

        case 'list':
            echo $twig->render('list.twig', 
            [
                'entries' => $entries,
                'title_order' => nextOrder('title', $sortField, $order),
                'mood_date_order' => nextOrder('mood_date', $sortField, $order),
                'mood_type_order' => nextOrder('mood_type', $sortField, $order),
                'energy_level_order' => nextOrder('energy_level', $sortField, $order),
                'author_order' => nextOrder('author', $sortField, $order),
                'created_at_order' => nextOrder('created_at', $sortField, $order),
            ]);
            break;

        default:
            echo $twig->render('form.twig');
            break;
    }

    exit;
}



switch ($page)
{
    case 'submit':
        $handler = new FormHandler(new Validator(), $storage);
        $result = $handler->handle($_POST);

        $pageTitle = 'Результат отправки';
        $contentTemplate = __DIR__ . '/../templates_php/result.php';
        break;

    case 'list':
        $pageTitle = 'Список записей дневника настроения';
        $contentTemplate = __DIR__ . '/../templates_php/list.php';
        $wide = true;
        break;

    default:
        $pageTitle = 'Дневник настроения - Добавление записи';
        $contentTemplate = __DIR__ . '/../templates_php/form.php';
        break;
}


require __DIR__ . '/../templates_php/layout.php';


