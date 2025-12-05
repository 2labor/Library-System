<?php
/**
 * Purpose: Interface defining book reservation HTTP endpoints.
 *
 * Responsibilities:
 * - Declare method signatures for reservation flows (reserve, cancel, extend, list)
 * - Define contracts for user-specific reservation queries
 *
 * Inputs:
 * - Path parameters (reservationId, bookId, userId) defined in signatures
 *
 * Outputs:
 * - Methods return void; implementations emit JSON with reservation DTOs
 *
 * File: app/controller/BookReservationController.php
 */

namespace App\Controller;

interface BookReservationController {
  public function reserve(): void;
  public function extend(): void;
  public function cancel(): void;
  public function getByBook(string $isbn): void;
  public function getByUser(int $userId): void;
}
