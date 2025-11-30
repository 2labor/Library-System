<?php 
namespace App\Repository;

use App\Domain\Entity\BookReserved;

interface BookReservedRepository {
  public function create(BookReserved $bookReserved): BookReserved;
  public function update(BookReserved $bookReserved): BookReserved;
  public function delete(int $id): bool;
 
  public function findReservationByBookIsbn(string $bookIsbn): ?BookReserved;
  public function findReservationById(int $reservationId): ?BookReserved;
  public function findReservationsByUserId(int $userId): array;
  public function findActiveReservationByBookIsbn(string $isbn): ?BookReserved;
}