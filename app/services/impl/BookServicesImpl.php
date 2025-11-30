<?php 
namespace App\Services\Impl;

use App\Repository\BookRepository;
use App\Services\BookServices;
use App\Domain\Entity\Book;
use Exception;

require_once __DIR__ . '/../BookServices.php';

class BookServicesImpl implements BookServices {

  private BookRepository $repo;

  public function __construct(BookRepository $repo) {
    $this->repo = $repo;
  }

  public function addBook(Book $book):  Book{
    return $this->repo->create($book);
  }

  public function updateBook(Book $book): ?Book{
    return $this->repo->update($book);
  }

  public function deleteBook(string $isbn): bool {
    return $this->repo->delete($isbn);
  }

  public function findBook(array $criteria): array{
    return $this->repo->findByPartial($criteria);
  }

  public function findBookByIsbn(string $isbn): ?Book {
    return $this->repo->findBookByIsbn($isbn);
  }

  public function findAllAvailableBooks(): array{
    return $this->repo->findAllAvailable();
  }

  public function toggleBookAvailability(string $isbn): bool {
    return $this->repo->toggleAvailability($isbn);
  }
}