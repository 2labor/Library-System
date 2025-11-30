<?php
session_start();
error_reporting(E_ALL & ~E_DEPRECATED);

// Database connection
require_once __DIR__ . '/../../../core/Database.php';

// Account + User classes
require_once __DIR__ . '/../entity/Account.php';
require_once __DIR__ . '/../entity/User.php';
require_once __DIR__ . '/../entity/AccountToken.php';
require_once __DIR__ . '/../mapper/AccountMapper.php';
require_once __DIR__ . '/../mapper/impl/AccountMapperImpl.php';
require_once __DIR__ . '/../mapper/UserMapper.php';
require_once __DIR__ . '/../mapper/impl/UserMapperImpl.php';
require_once __DIR__ . '/../../repository/AccountRepository.php';
require_once __DIR__ . '/../../repository/impl/AccountRepositoryImpl.php';
require_once __DIR__ . '/../../repository/UserRepository.php';
require_once __DIR__ . '/../../repository/impl/UserRepositoryImpl.php';
require_once __DIR__ . '/../../repository/AccountTokenRepository.php';
require_once __DIR__ . '/../../repository/impl/AccountTokenRepositoryImpl.php';
require_once __DIR__ . '/../../services/AccountServices.php';
require_once __DIR__ . '/../../services/impl/AccountServicesImpl.php';
require_once __DIR__ . '/../../services/UserServices.php';
require_once __DIR__ . '/../../services/impl/UserServicesImpl.php';
require_once __DIR__ . '/../../services/AccountTokenServices.php';
require_once __DIR__ . '/../../services/impl/AccountTokenServicesImpl.php';
require_once __DIR__ . '/../../email/EmailTemplateRenderer.php';
require_once __DIR__ . '/../../services/EmailServices.php';
require_once __DIR__ . '/../../services/impl/EmailServicesImpl.php';


// Book classes - INTERFACES FIRST
require_once __DIR__ . '/../entity/Book.php';
require_once __DIR__ . '/../entity/BookReserved.php';
require_once __DIR__ . '/../entity/Category.php';
require_once __DIR__ . '/../mapper/BookMapper.php';
require_once __DIR__ . '/../mapper/BookReservedMapper.php';
require_once __DIR__ . '/../mapper/impl/BookMapperImpl.php';
require_once __DIR__ . '/../mapper/impl/BookReservedMapperImpl.php';
require_once __DIR__ . '/../../services/BookServices.php';
require_once __DIR__ . '/../../services/BookReservedServices.php';
require_once __DIR__ . '/../../services/impl/BookServicesImpl.php';
require_once __DIR__ . '/../../services/impl/BookReservationServicesImpl.php';
require_once __DIR__ . '/../../repository/BookRepository.php';
require_once __DIR__ . '/../../repository/impl/BookRepositoryImpl.php';
require_once __DIR__ . '/../../repository/BookReservedRepository.php';
require_once __DIR__ . '/../../repository/impl/BookReservedRepositoryImpl.php';

// Config
$config = require __DIR__ . '/../../../core/config.php';

$emailRenderer = new EmailTemplateRenderer();
$emailService = new EmailServicesImpl($config, $emailRenderer);

// Сервисы пользователя
$accountMapper = new AccountMapperImpl();
$accountRepo = new AccountRepositoryImpl($pdo, $accountMapper);
$userMapper = new UserMapperImpl($accountMapper, $accountRepo);
$userRepo = new UserRepositoryImpl($userMapper, $pdo);
$accountTokenRepo = new AccountTokenRepositoryImpl($pdo);
$tokenService = new AccountTokenServicesImpl($accountTokenRepo, $accountRepo);
$accountService = new AccountServicesImpl($accountRepo, $tokenService);
$userService = new UserServicesImpl($userRepo, $accountService, $tokenService, $emailService);

// Сервисы книг
$bookMapper = new BookMapperImpl();
$bookRepo   = new BookRepositoryImpl($pdo, $bookMapper);

$bookReservedMapper = new BookReservedMapperImpl();
$bookReservedRepo   = new BookReservedRepositoryImpl($pdo, $bookReservedMapper);

$bookService = new BookServicesImpl($bookRepo);
$bookResService = new BookReservationServicesImpl($bookReservedMapper, $bookReservedRepo, $userRepo, $bookRepo ,$emailService);

try {
    echo "Connected<br>";

    // Чистим таблицы
    $pdo->exec("DELETE FROM books_reserved");
    $pdo->exec("DELETE FROM books");
    $pdo->exec("DELETE FROM account_tokens");
    $pdo->exec("DELETE FROM users");
    $pdo->exec("DELETE FROM accounts");

    echo "<h3>Таблицы очищены</h3>";

    // Создаём аккаунт
    $account = $accountService->registerAccount(
        "Catty",
        // "alicekurinna@gmail.com",
        "yaroslavkliutko@gmail.com",
        "123456",
        "123456",
        1234567890,
        9876543210
    );

    echo "<h2>Аккаунт создан:</h2><pre>";
    print_r($account);
    echo "</pre>";

    // Создаем пользователя
    $user = $userService->createUser(
        "Olena Kliutko",
        "Mey",
        "123 Main St",
        "",
        null,
        "City",
        $account
    ); 

    echo "<h2>Пользователь создан:</h2><pre>";
    print_r($user);
    echo "</pre>";

    // Верификация email
    $tokens = $tokenService->getTokensByAccountIdAndType($account->getId(), 'verify_email');
    $verifyToken = $tokens[0];
    $accountService->verifyEmail($account->getId(), $verifyToken->getCode());

    echo "<h2>Email подтверждён</h2>";

    // Сброс пароля
    $userService->resetPassword($account->getEmail());

    $resetTokens = $tokenService->getTokensByAccountIdAndType($account->getId(), 'reset_password');
    $resetToken = $resetTokens[0];
    $userService->resetPasswordWithToken($resetToken->getToken(), "newStrongPassword123");

    echo "<h2>Пароль успешно сброшен</h2>";

    // ЛОГИН
    $loginUser = $userService->login($account->getEmail(), "newStrongPassword123");

    echo "<h2>Успешный вход:</h2><pre>";
    print_r($loginUser);
    echo "</pre>";

    // ============================================
    //              РАБОТА С КНИГАМИ
    // ============================================

    echo "<h1>=== Добавляем реальные книги ===</h1>";

    $fantasyCategory = new Category('Fantasy', 4);
    $adventureCategory = new Category('Adventure', 5);
    $fictionCategory = new Category('Fiction', 6);

    $booksToAdd = [
        [
            'isbn' => '9780545010221',
            'title' => 'Looking After Cats and Kittens',
            'author' => 'J. K. Rowling',
            'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRP6_iGsOFwNyjr83tZkavmr9LbciCTbJLUJw&s',
            'edition' => '1',
            'year' => 2007,
            'category' => $adventureCategory
        ],
        [
            'isbn' => '9780261103573',
            'title' => 'The Lord of the Rings',
            'author' => 'J. R. R. Tolkien',
            'image' => 'https://example.com/lotr.jpg',
            'edition' => '3',
            'year' => 1954,
            'category' => $fictionCategory
        ],
        [
            'isbn' => '9780143126560',
            'title' => 'The Martian',
            'author' => 'Andy Weir',
            'image' => 'https://example.com/martian.jpg',
            'edition' => '1',
            'year' => 2011,
            'category' => $fictionCategory
        ]
    ];

    foreach ($booksToAdd as $book) {
        $newBook = new Book(
            $book['isbn'],
            $book['title'],
            $book['image'],
            $book['author'],
            $book['edition'],
            $book['year'],
            true,
            $book['category'],
        );
        $bookService->addBook($newBook);
    }

    echo "<h2>Книги добавлены</h2>";

    // Показать все книги
    $allBooks = $bookService->findAllAvailableBooks();
    echo "<h2>Все книги:</h2><pre>";
    print_r($allBooks);
    echo "</pre>";

    // Резервируем книгу
    echo "<h1>=== Резервируем книгу Harry Potter ===</h1>";

    $reserve = $bookResService->reserveBook(
        '9780545010221',
        $user->getId()
    );

    echo "<h2>Book reserved:</h2><pre>";
    print_r($reserve);
    echo "</pre>";

    // Список резервов
    $resAll = $bookResService->getReservationByUserId($user->getId());

    echo "<h2>Резервации пользователя:</h2><pre>";
    print_r($resAll);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2>Ошибка:</h2> " . $e->getMessage();
}

// --- ПРОДЛЕНИЕ РЕЗЕРВАЦИИ ---
echo "<h1>=== Пробуем продлить резервацию ===</h1>";

try {
    $extended = $bookResService->extendReservation($reserve);
    echo "<h2>Резервация продлена:</h2><pre>";
    print_r($extended);
    echo "</pre>";

    // Попробуем продлить снова — должно выдать исключение
    try {
        $bookResService->extendReservation($reserve);
    } catch (Exception $e) {
        echo "<h2>Повторное продление запрещено:</h2> " . $e->getMessage() . "<br>";
    }
} catch (Exception $e) {
    echo "<h2>Ошибка при продлении:</h2> " . $e->getMessage() . "<br>";
}

// --- ЗАПРЕТ ДВОЙНОГО РЕЗЕРВИРОВАНИЯ ---
echo "<h1>=== Пробуем зарезервировать ту же книгу снова ===</h1>";
try {
    $bookResService->reserveBook('9780545010221', $user->getId());
} catch (Exception $e) {
    echo "<h2>Двойное резервирование запрещено:</h2> " . $e->getMessage() . "<br>";
}

// --- ОТМЕНА РЕЗЕРВАЦИИ ---
echo "<h1>=== Отмена резервации ===</h1>";
try {
    $cancelled = $bookResService->cancelReservation($reserve->getId());
    echo "<h2>Резервация отменена:</h2><pre>";
    var_export($cancelled);
    echo "</pre>";

    // Проверим, что книгу теперь можно зарезервировать снова
    $newReserve = $bookResService->reserveBook('9780545010221', $user->getId());
    echo "<h2>Книга снова зарезервирована:</h2><pre>";
    print_r($newReserve);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2>Ошибка при отмене резервации:</h2> " . $e->getMessage() . "<br>";
}
