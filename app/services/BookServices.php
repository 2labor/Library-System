<?php
/*
 * Inputs:
 * - Methods for book management operations
 *
 * Outputs:
 * - Book entity or boolean status for operations
 *
 * File: app/services/BookServices.php
 */
namespace App\Services;

use App\Domain\Entity\Book;

interface BookServices {
  public function addBook(Book $book):  Book;
  public function updateBook(Book $book): ?Book;
  public function deleteBook(string $isbn): bool;
  public function findBook(array $criteria): array;
  public function findAllAvailableBooks(): array;
  public function toggleBookAvailability(string $isbn): bool;
}