<?php 
/*
 * Inputs:
 * - Methods for book management operations
 *
 * Outputs:
 * - Book entity or boolean status for operations
 *
 * File: app/services/impl/BookServicesImpl.php
 */
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

  /**
   * Adds a new book to the repository.
   *
   * @param Book $book The Book entity to add.
   * @return Book The added Book entity.
   * @throws Exception If there is an error during the addition.
   */
  public function addBook(Book $book):  Book{
    return $this->repo->create($book);
  }

  /**
   * Updates an existing book in the repository.
   *
   * @param Book $book The Book entity to update.
   * @return Book|null The updated Book entity, or null if not found.
   * @throws Exception If there is an error during the update.
   */
  public function updateBook(Book $book): ?Book{
    return $this->repo->update($book);
  }

  /**
   * Deletes a book from the repository by its ISBN.
   *
   * @param string $isbn The ISBN of the book to delete.
   * @return bool True if the book was successfully deleted, false otherwise.
   * @throws Exception If there is an error during the deletion.
   */
  public function deleteBook(string $isbn): bool {
    return $this->repo->delete($isbn);
  }

  /**
   * Finds books based on given criteria.
   *
   * @param array $criteria The criteria to search for books.
   * @return array An array of Book entities matching the criteria.
   * @throws Exception If there is an error during the search.
   */
  public function findBook(array $criteria): array{
    return $this->repo->findByPartial($criteria);
  }

  /**
   * Finds a book by its ISBN.
   *
   * @param string $isbn The ISBN of the book to find.
   * @return Book|null The found Book entity, or null if not found.
   * @throws Exception If there is an error during the search.
   */
  public function findBookByIsbn(string $isbn): ?Book {
    return $this->repo->findBookByIsbn($isbn);
  }

  /**
   * Retrieves all available books from the repository.
   *
   * @return array An array of available Book entities.
   * @throws Exception If there is an error during the retrieval.
   */
  public function findAllAvailableBooks(): array{
    return $this->repo->findAllAvailable();
  }

  /**
   * Toggles the availability status of a book by its ISBN.
   *
   * @param string $isbn The ISBN of the book to toggle availability.
   * @return bool True if the availability was successfully toggled, false otherwise.
   * @throws Exception If there is an error during the operation.
   */
  public function toggleBookAvailability(string $isbn): bool {
    return $this->repo->toggleAvailability($isbn);
  }
}