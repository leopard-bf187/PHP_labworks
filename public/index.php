<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Csrf.php';
require_once __DIR__ . '/../src/Core/Middleware.php';
require_once __DIR__ . '/../src/Core/Captcha.php';

require_once __DIR__ . '/../src/Repository/UserRepository.php';
require_once __DIR__ . '/../src/Repository/MoodEntryRepository.php';

require_once __DIR__ . '/../src/Validator/AuthValidator.php';
require_once __DIR__ . '/../src/Validator/MoodEntryValidator.php';

require_once __DIR__ . '/../src/Service/AuthService.php';
require_once __DIR__ . '/../src/Service/MoodEntryFormHandler.php';

require_once __DIR__ . '/../src/helpers.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

$pdo = Database::getConnection();

$userRepository = new UserRepository($pdo);
$entryRepository = new MoodEntryRepository($pdo);

$authService = new AuthService(
    $userRepository,
    new AuthValidator()
);

$entryFormHandler = new MoodEntryFormHandler(
    new MoodEntryValidator(),
    $entryRepository
);

$loader = new FilesystemLoader(__DIR__ . '/../templates_twig');
$twig = new Environment($loader);

$twig->addFilter(new TwigFilter('energy_label', fn(string $level) => energyLabel($level)));

$page = $_GET['page'] ?? 'home';

$csrfToken = Csrf::token();
$currentUser = $authService->user();

/**
 * Выполняет редирект.
 *
 * @param string $url URL
 * @return void
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Проверяет POST-запрос.
 *
 * @return bool
 */
function isPost(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/**
 * Проверяет CSRF.
 *
 * @return void
 */
function requireValidCsrf(): void
{
    if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Ошибка безопасности: неверный CSRF-токен.';
        exit;
    }
}

/**
 * Общие данные для шаблонов.
 *
 * @return array
 */
function baseViewData(): array
{
    return [
        'csrf_token' => Csrf::token(),
        'current_user' => $_SESSION['user'] ?? null,
        'is_admin' => ($_SESSION['user']['role'] ?? '') === 'admin',
    ];
}

switch ($page) {
    /*
    |--------------------------------------------------------------------------
    | Public pages
    |--------------------------------------------------------------------------
    */

    case 'home':
        echo $twig->render('home.twig', array_merge(baseViewData(), [
            'latest_entries' => $entryRepository->getLatestPublicRecords(5),
            'stats' => $entryRepository->getPublicStats(),
        ]));
        break;

    case 'login':
        echo $twig->render('auth/login.twig', array_merge(baseViewData(), [
            'errors' => [],
            'old' => [],
            'captcha_question' => Captcha::question(),
        ]));
        break;

    case 'login_store':
        if (!isPost()) {
            redirect('index.php?page=login');
        }

        requireValidCsrf();

        if (!Captcha::validate($_POST['captcha'] ?? null)) {
            echo $twig->render('auth/login.twig', array_merge(baseViewData(), [
                'errors' => ['Неверный ответ CAPTCHA.'],
                'old' => $_POST,
                'captcha_question' => Captcha::question(),
            ]));
            break;
        }

        Captcha::clear();

        $result = $authService->login($_POST);

        if ($result['success']) {
            redirect('index.php?page=list');
        }

        echo $twig->render('auth/login.twig', array_merge(baseViewData(), [
            'errors' => $result['errors'],
            'old' => $_POST,
        ]));
        break;

    case 'register':
        echo $twig->render('auth/register.twig', array_merge(baseViewData(), [
            'errors' => [],
            'old' => [],
            'action' => 'index.php?page=register_store',
            'title' => 'Регистрация',
            'captcha_question' => Captcha::question(),
        ]));
        break;

    case 'register_store':
        if (!isPost()) {
            redirect('index.php?page=register');
        }

        requireValidCsrf();

        if (!Captcha::validate($_POST['captcha'] ?? null)) {
            echo $twig->render('auth/register.twig', array_merge(baseViewData(), [
                'errors' => ['Неверный ответ CAPTCHA.'],
                'old' => $_POST,
                'action' => 'index.php?page=register_store',
                'title' => 'Регистрация',
                'captcha_question' => Captcha::question(),
            ]));
            break;
        }
            
        Captcha::clear();

        $result = $authService->register($_POST);

        if ($result['success']) {
            redirect('index.php?page=login');
        }

        echo $twig->render('auth/register.twig', array_merge(baseViewData(), [
            'errors' => $result['errors'],
            'old' => $_POST,
            'action' => 'index.php?page=register_store',
            'title' => 'Регистрация',
        ]));
        break;

    case 'logout':
        $authService->logout();
        redirect('index.php?page=home');
        break;

    /*
    |--------------------------------------------------------------------------
    | Mood diary protected pages
    |--------------------------------------------------------------------------
    */

    case 'list':
        Middleware::requireAuth();

        $sortField = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'desc';
        $query = trim($_GET['q'] ?? '');

        if (Middleware::isAdmin()) {
            $entries = $query !== ''
                ? $entryRepository->searchAllRecords($query)
                : $entryRepository->getAllRecords($sortField, $order);
        } else {
            $entries = $query !== ''
                ? $entryRepository->searchRecordsByUserId(Middleware::userId(), $query)
                : $entryRepository->getRecordsByUserId(Middleware::userId(), $sortField, $order);
        }

        echo $twig->render('entries/list.twig', array_merge(baseViewData(), [
            'entries' => $entries,
            'sort' => $sortField,
            'order' => $order,
            'query' => $query,
        ]));
        break;

    case 'create':
        Middleware::requireAuth();

        echo $twig->render('entries/form.twig', array_merge(baseViewData(), [
            'mode' => 'create',
            'action' => 'index.php?page=store',
            'entry' => null,
            'moods' => $entryRepository->getAllMoods(),
            'errors' => [],
        ]));
        break;

    case 'store':
        Middleware::requireAuth();

        if (!isPost()) {
            redirect('index.php?page=create');
        }

        requireValidCsrf();

        $result = $entryFormHandler->create(Middleware::userId(), $_POST);

        if ($result['success']) {
            redirect('index.php?page=list');
        }

        echo $twig->render('entries/form.twig', array_merge(baseViewData(), [
            'mode' => 'create',
            'action' => 'index.php?page=store',
            'entry' => $_POST,
            'moods' => $entryRepository->getAllMoods(),
            'errors' => $result['errors'],
        ]));
        break;

    case 'edit':
        Middleware::requireAuth();

        $id = (int)($_GET['id'] ?? 0);

        $entry = Middleware::isAdmin()
            ? $entryRepository->getRecordById($id)
            : $entryRepository->getRecordByIdAndUserId($id, Middleware::userId());

        if (!$entry) {
            http_response_code(404);
            echo $twig->render('error.twig', array_merge(baseViewData(), [
                'message' => 'Запись не найдена или доступ запрещён.',
            ]));
            break;
        }

        echo $twig->render('entries/form.twig', array_merge(baseViewData(), [
            'mode' => 'edit',
            'action' => 'index.php?page=update&id=' . $id,
            'entry' => $entry,
            'moods' => $entryRepository->getAllMoods(),
            'errors' => [],
        ]));
        break;

    case 'update':
        Middleware::requireAuth();

        if (!isPost()) {
            redirect('index.php?page=list');
        }

        requireValidCsrf();

        $id = (int)($_GET['id'] ?? 0);

        $result = $entryFormHandler->update(
            $id,
            Middleware::userId(),
            Middleware::isAdmin(),
            $_POST
        );

        if ($result['success']) {
            redirect('index.php?page=list');
        }

        echo $twig->render('entries/form.twig', array_merge(baseViewData(), [
            'mode' => 'edit',
            'action' => 'index.php?page=update&id=' . $id,
            'entry' => array_merge($_POST, ['id' => $id]),
            'moods' => $entryRepository->getAllMoods(),
            'errors' => $result['errors'],
        ]));
        break;

    case 'delete':
        Middleware::requireAuth();

        if (!isPost()) {
            redirect('index.php?page=list');
        }

        requireValidCsrf();

        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            if (Middleware::isAdmin()) {
                $entryRepository->deleteRecord($id);
            } else {
                $entryRepository->deleteRecordByUserId($id, Middleware::userId());
            }
        }

        redirect('index.php?page=list');
        break;

    /*
    |--------------------------------------------------------------------------
    | Admin pages
    |--------------------------------------------------------------------------
    */

    case 'admin_users':
        Middleware::requireAdmin();

        echo $twig->render('admin/users.twig', array_merge(baseViewData(), [
            'users' => $userRepository->findAll(),
        ]));
        break;

    case 'admin_create_user':
        Middleware::requireAdmin();

        echo $twig->render('auth/register.twig', array_merge(baseViewData(), [
            'errors' => [],
            'old' => [],
            'action' => 'index.php?page=admin_store_user',
            'title' => 'Создание администратора',
        ]));
        break;

    case 'admin_store_user':
        Middleware::requireAdmin();

        if (!isPost()) {
            redirect('index.php?page=admin_create_user');
        }

        requireValidCsrf();

        $result = $authService->createAdmin($_POST);

        if ($result['success']) {
            redirect('index.php?page=admin_users');
        }

        echo $twig->render('auth/register.twig', array_merge(baseViewData(), [
            'errors' => $result['errors'],
            'old' => $_POST,
            'action' => 'index.php?page=admin_store_user',
            'title' => 'Создание администратора',
        ]));
        break;

    case 'admin_entries':
        Middleware::requireAdmin();

        $sortField = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'desc';
        $query = trim($_GET['q'] ?? '');

        $entries = $query !== ''
            ? $entryRepository->searchAllRecords($query)
            : $entryRepository->getAllRecords($sortField, $order);

        echo $twig->render('entries/list.twig', array_merge(baseViewData(), [
            'entries' => $entries,
            'sort' => $sortField,
            'order' => $order,
            'query' => $query,
        ]));
        break;

    case 'admin_delete_user':
        Middleware::requireAdmin();

        if (!isPost()) {
            redirect('index.php?page=admin_users');
        }

        requireValidCsrf();

        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0 && $id !== Middleware::userId()) {
            $userRepository->deleteById($id);
        }

        redirect('index.php?page=admin_users');
        break;

    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    */

    default:
        http_response_code(404);
        echo $twig->render('error.twig', array_merge(baseViewData(), [
            'message' => 'Страница не найдена.',
        ]));
        break;
}


