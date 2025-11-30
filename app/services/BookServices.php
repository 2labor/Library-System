<?php
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