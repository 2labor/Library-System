<?php
declare(strict_types=1);

ob_start();

// CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 3600');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit;
}

error_reporting(E_ALL & ~E_DEPRECATED);

require_once __DIR__ . '/../vendor/autoload.php';

ob_clean();

require_once __DIR__ . '/../core/Database.php';

ob_clean();

$config = require __DIR__ . '/../core/config.php';

/* =====================  IMPORTS  ===================== */

use App\Repository\Impl\AccountRepositoryImpl;
use App\Repository\Impl\AccountTokenRepositoryImpl;
use App\Domain\Mapper\Impl\AccountMapperImpl;
use App\Services\Impl\AccountServicesImpl;
use App\Services\Impl\AccountTokenServicesImpl;
use App\Services\Impl\EmailServicesImpl;
use App\Controller\Impl\AccountControllerImpl;
use App\Routers\RouterAccount;

use App\Repository\Impl\UserRepositoryImpl;
use App\Domain\Mapper\Impl\UserMapperImpl;
use App\Services\Impl\UserServicesImpl;
use App\Controller\Impl\UserControllerImpl;
use App\Routers\RouterUser;

use App\Repository\Impl\BookRepositoryImpl;
use App\Domain\Mapper\Impl\BookMapperImpl;
use App\Services\Impl\BookServicesImpl;
use App\Controller\Impl\BookControllerImpl;
use App\Routers\RouterBook;

use App\Repository\Impl\BookReservedRepositoryImpl;
use App\Domain\Mapper\Impl\BookReservedMapperImpl;
use App\Services\Impl\BookReservationServicesImpl;
use App\Controller\Impl\BookReservationControllerImpl;
use App\Routers\RouterBookReservation;

use App\Repository\Impl\CategoriesRepositoryImpl;
use App\Controller\Impl\CategoriesControllerImpl;
use App\Routers\RouterCategories;

/* =====================  INITIALIZATION ===================== */

// Categories
$categoryRepo        = new CategoriesRepositoryImpl($pdo);
$categoriesController = new CategoriesControllerImpl($categoryRepo);

// Categories router
$routerCategories = new RouterCategories($categoriesController);

// Account
$accountMapper  = new AccountMapperImpl();
$accountRepo    = new AccountRepositoryImpl($pdo, $accountMapper);
$tokenRepo      = new AccountTokenRepositoryImpl($pdo);
$tokenService   = new AccountTokenServicesImpl($tokenRepo, $accountRepo);

// email service
$emailService   = new EmailServicesImpl($config, new App\Email\EmailTemplateRenderer());

// User
$userMapper     = new UserMapperImpl($accountMapper, $accountRepo);
$userRepo       = new UserRepositoryImpl($userMapper, $pdo);

// Services
$accountService = new AccountServicesImpl($accountRepo, $tokenService, $emailService, $userRepo);
$userService    = new UserServicesImpl($userRepo, $accountService);

// Books
$bookMapper     = new BookMapperImpl();
$bookRepo       = new BookRepositoryImpl($pdo, $bookMapper);
$bookService    = new BookServicesImpl($bookRepo);

// Controllers
$accountController = new AccountControllerImpl($accountService);
$userController    = new UserControllerImpl($userService, $accountService);
$bookController    = new BookControllerImpl($bookService, $bookMapper);

// Routers
$routerAccount = new RouterAccount($accountController);
$routerUser    = new RouterUser($userController);
$routerBook    = new RouterBook($bookController);

/* ===================== RESERVATION SETUP  ===================== */

// Reservation mapper + repo + service
$reservedMapper  = new BookReservedMapperImpl();
$reservedRepo    = new BookReservedRepositoryImpl($pdo, $reservedMapper);

$reservationService = new BookReservationServicesImpl(
    $reservedRepo,
    $userRepo,
    $bookRepo,
    $emailService
);

// Reservation controller
$reservationController = new BookReservationControllerImpl($reservationService);

// Reservation router
$routerReservation = new RouterBookReservation($reservationController);


/* =====================  ROUTING  ===================== */

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . $uri;
    if (is_file($file)) return false;
}

header('Content-Type: application/json; charset=utf-8');

if (str_starts_with($uri, '/accounts')) {
    $routerAccount->handle($method, $uri);

} elseif (str_starts_with($uri, '/users')) {
    $routerUser->handle($method, $uri);

} elseif (str_starts_with($uri, '/books')) {
    $routerBook->handle($method, $uri);

} elseif (str_starts_with($uri, '/reservation')) { 
    $routerReservation->handle($method, $uri);

} elseif (str_starts_with($uri, '/categories')) { 
    $routerCategories->handle($method, $uri);

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
