<?php
/*
 * Inputs:
 * - Methods to perform CRUD operations on Book entities
 *
 * Outputs:
 * - Book entities retrieved or modified in the database
 *
 * File: app/repository/BookRepository.php
 */
namespace App\Repository;

use App\Domain\Entity\Book;

interface BookRepository {
  public function create(Book  $book): Book;
  public function update(Book $book): Book;
  public function delete(string $isbn): bool;

  public function findBookByIsbn(string $isbn): ?Book;
  public function findByPartial(array $criteria): array;
  public function findAllAvailable(): array;

  public function toggleAvailability(string $isbn): bool;
}