<?php
/*
 * Inputs:
 * - Methods for book reservation operations
 *
 * Outputs:
 * - BookReserved entity or boolean status for operations
 *
 * File: app/services/BookReservedServices.php
 */
namespace App\Services;

use App\Domain\Entity\BookReserved;

interface BookReservedServices {
  public function reserveBook(string $isbn, int $userId): BookReserved;
  public function extendReservation(BookReserved $reservation): BookReserved;
  public function cancelReservation(int $reservationId): bool;
  public function getReservationByBook(string $isbn): ?BookReserved;
  public function getReservationByUserId(int $userId): array; 
}